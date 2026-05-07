<?php

namespace App\Service;

use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\ProjectRole;
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
         private AppUserRepository $userRepository,
         private ProjectRoleRepository $roleRepository,
         private ProjectUserRepository $puRepository,
     ) {}

    /**
     * @throws Exception
     */
    public function assignUser(Project $project, AppUser $user, ProjectRole $role): ProjectUser
    {
        if ($this->puRepository->findOneByProjectAndUser($project, $user)) {
            throw new Exception('Project already assigned to user.');
        }

        $assignment = (new ProjectUser())
                ->setProject($project)
                ->setAppUser($user)
                ->setProjectRole($role)
                ->setProjectRole($role);

        $this->em->persist($assignment);
        $this->em->flush();

        return $assignment;

    }

    /**
     * @throws Exception
     */
    public function updateAssignment(ProjectUser $assignment, array $data): ProjectUser
    {
        if (isset($data['role_id'])) {
            $role = $this->roleRepository->find($data['role_id']);
            if (!$role) throw new Exception('Role not found', 404);
            $assignment->setProjectRole($role);
        }

        if (isset($data['is_active'])) {
            $assignment->setIsActive($data['is_active']);
        }

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
                    if ($role) $currentAssignments[$userId]->setProjectRole($role);
                    unset($currentAssignments[$userId]);
                } else {
                    $newUser = $this->userRepository->find($userId);
                    $role = $this->roleRepository->find($roleId);

                    if($newUser && $role) {
                        $assignment = ( new ProjectUser())
                            ->setProject($project)
                            ->setAppUser($newUser)
                            ->setProjectRole($role);
                        $this->em->persist($assignment);
                    }
                }
            }
            #Ask if wanted to be removed or disabled
            foreach ($currentAssignments as $oldAssignment) {
                $this->em->remove($oldAssignment);
            }

            $this->em->flush();
        });



    }

    /**
     * @throws Exception
     */
    public function removeAssignment(ProjectUser $assignment): void
    {

        $this->em->remove($assignment);
        $this->em->flush();

    }

    /**
     * @throws Exception
     */
    public function deactivateAssignment(ProjectUser $assignment): void
    {

        $assignment->setIsActive(false);
        $this->em->flush();

    }

    /**
     * @throws Exception
     */
    public function findAssignment(Project $project, AppUser $user): ProjectUser
    {
        $assignment = $this->puRepository->findOneByProjectAndUser($project, $user);

        if (!$assignment) {
            throw new Exception('Project assignment not found for this user', 404);
        }

        return $assignment;
    }



}
