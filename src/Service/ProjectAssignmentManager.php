<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Repository\AppUserRepository;
use App\Repository\ProjectRoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProjectAssignmentManager
{
     public function __construct(
         private EntityManagerInterface $em,
         private AppuserRepository $userRepository,
         private ProjectRoleRepository $roleRepository
     )
     {}

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

}
