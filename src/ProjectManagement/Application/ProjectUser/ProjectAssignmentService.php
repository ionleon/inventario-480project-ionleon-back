<?php

namespace App\ProjectManagement\Application\ProjectUser;

use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use Exception;

readonly class ProjectAssignmentService
{
     public function __construct(
         private ProjectUserRepositoryInterface $puRepository,
         private ProjectRoleRepositoryInterface $roleRepository,
         private AppUserRepositoryInterface $userRepository,
     ) {}

    /**
     * @throws Exception
     */
    public function assignUser(Project $project, string $userId, string $roleId): ProjectUser
    {
        if ($this->puRepository->findOneByProjectAndUser($project->getId(), $userId)) {
            throw new Exception('Project already assigned to user.', 409);
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) throw new Exception('User not found.');

        $role = $this->roleRepository->findById($roleId);
        if (!$role) throw new Exception('Role not found.');

        $assignment = ProjectUser::create(
            $project,
            $user,
            $role,
            true
        );

        $this->puRepository->save($assignment);

        return $assignment;

    }


    /**
     * @throws Exception
     */
    public function updateAssignment(ProjectUser $assignment, array $data): ProjectUser
    {

        $role = isset($data['role_id'])
            ? $this->roleRepository->findById($data['role_id'])
            : $assignment->getProjectRole();

        if (!$role) throw new Exception('Role not found.');

        $isActive = $data['is_active'] ?? $assignment->isActive();

        $assignment->update($role, (bool)$isActive);

        $this->puRepository->save($assignment);

        return $assignment;
    }

    /**
     * @throws Exception
     */
    public function deactivateAssignment(ProjectUser $assignment, bool $isActive): ProjectUser
    {
        $assignment->setIsActive($isActive);
        $this->puRepository->save($assignment);

        return $assignment;
    }

    public function syncProjectUsers(Project $project, array $userData): void
    {
        $this->puRepository->transaction(function () use ($project, $userData) {
            $currentAssignments = [];
            foreach ($project->getProjectUsers() as $assignment) {
                $currentAssignments[$assignment->getAppUser()->getId()->toRfc4122()] = $assignment;
            }

            foreach ($userData as $data) {
                $userId = $data['user_id'];

                $role = $this->roleRepository->findById($data['role_id']);

                if (isset($currentAssignments[$userId])) {
                    $currentAssignments[$userId]->update($role, (bool) ($data['is_active'] ?? true));
                    unset($currentAssignments[$userId]);
                } else {
                    $newUser = $this->userRepository->findById($userId);
                    $assignment = new ProjectUser($project, $newUser, $role);

                    $this->puRepository->save($assignment,false);
                }
            }

            #Ask if wanted to be removed or disabled
            foreach ($currentAssignments as $oldAssignment) {
                $this->puRepository->remove($oldAssignment);
            }

        });

    }

}
