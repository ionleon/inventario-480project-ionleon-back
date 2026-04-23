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


        $exists = $this->puRepository->findOneBy([
           'project' => $project,
           'appUser' => $userId
        ]);

        if ($exists) {
            throw new Exception('user is already assigned', 409);
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
        $currentAssignments = [];
        foreach ($project->getProjectUsers() as $assignment) {
            $currentAssignments[$assignment->getAppUser()->getId()->toRfc4122()] = $assignment;
        }

        foreach ($userData as $data) {
            $userId = $data['user_id'];
            $roleId = $data['role_id'];

            if (isset($currentAssignments[$userId])) {
                $role = $this->roleRepository->find($roleId);
                $currentAssignments[$userId]->setProjectRole($role);
                unset($currentAssignments[$userId]);
            } else {
                $newUser = $this->userRepository->find($userId);
                $role = $this->roleRepository->find($roleId);

                $assignment = new ProjectUser();
                $assignment->setProject($project);
                $assignment->setAppUser($newUser);
                $assignment->setProjectRole($role);

                $this->em->persist($assignment);
            }

            foreach ($currentAssignments as $oldAssignment) {
                $this->em->remove($oldAssignment);
            }

            $this->em->flush();

        }
    }

    /**
     * @throws Exception
     */
    public function removeAssignment(Project $project, string $userId): void
    {
        $user = $this->userRepository->find($userId);

        if (!$user ) {
            throw new Exception('User not found', 404);
        }

        $assignment = $this->puRepository->findOneBy([
           'project' => $project,
           'appUser' => $user
        ]);

        if ($assignment) {
            $this->em->remove($assignment);
            $this->em->flush();
        }


    }



}
