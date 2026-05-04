<?php

namespace App\Service;

use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Repository\AppUserRepository;
use App\Repository\ProjectRoleRepository;
use App\Repository\ProjectUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ProjectAssignmentManager
{
     public function __construct(
         private EntityManagerInterface $em,
         private AppuserRepository $userRepository,
         private ProjectRoleRepository $roleRepository,
         private ProjectUserRepository $puRepository,
     ) {}

    /**
     * @throws Exception
     */
    public function assignUser(Project $project, string $userId, string $roleId): ProjectUser
    {
        try {
            $user = $this->userRepository->find($userId);
            $role = $this->roleRepository->find($roleId);
        } catch(\Exception $e) {
            throw new \Exception("Invalid UUID format provided", 400, $e->getMessage());
        }

        if (!$user || !$role) {
            throw new Exception('User or Role not found', 404);
        }

        $exists = $this->puRepository->findOneByProjectAndUser($project, $user);

        if ($exists) {
            throw new Exception('User is already assigned', 409);
        }

        $assignment = new ProjectUser();
        $assignment->setProject($project);
        $assignment->setAppUser($user);
        $assignment->setProjectRole($role);

        $this->em->persist($assignment);
        $this->em->flush();

        return $assignment;

    }

    public function syncProjectUsers(Project $project, array $userData): void
    {
        $this->em->wrapInTransaction(function () use ($project, $userData) {
            $currentAssignments = [];
            foreach ($project->getProjectUsers() as $assignment) {
                $currentAssignments[$assignment->getAppUser()->getId()->toRfc4122()] = $assignment;
            }

            foreach ($userData as $data) {
                $userId = $data['user_id'] ?? null;
                $roleId = $data['role_id'] ?? null;

                if (!$userId || !$roleId) continue;

                if (isset($currentAssignments[$userId])) {
                    $role = $this->roleRepository->find($roleId);
                    if ($role) {
                        $currentAssignments[$userId]->setProjectRole($role);
                    }
                    unset($currentAssignments[$userId]);
                } else {
                    $newUser = $this->userRepository->find($userId);
                    $role = $this->roleRepository->find($roleId);

                    if (!$role) {
                        throw new Exception("Role with ID $roleId not found", 404);
                    }

                    if($newUser && $role) {
                        $assignment = new ProjectUser();
                        $assignment->setProject($project);
                        $assignment->setAppUser($newUser);
                        $assignment->setProjectRole($role);
                        $this->em->persist($assignment);
                    }
                }
            }

            foreach ($currentAssignments as $oldAssignment) {
                $this->em->remove($oldAssignment);
            }

            $this->em->flush();
        });



    }

    /**
     * @throws Exception
     */
    public function removeAssignment(Project $project, AppUser $user): void
    {

        $assignment = $this->puRepository->findOneByProjectAndUser($project, $user);

        if (!$assignment) {
            throw new Exception('Assignemt not found', 404);
        }

        $this->em->remove($assignment);
        $this->em->flush();

    }

}
