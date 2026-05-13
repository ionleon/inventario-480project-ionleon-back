<?php

namespace App\ProjectManagement\Application\SyncProjectUsers;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\UserManagement\Domain\AppUserRepositoryInterface;

final class SyncProjectUsersHandler
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly ProjectRoleRepositoryInterface $roleRepository,
    ) {}

    public function handle(SyncProjectUsersCommand $command): void
    {
        $project = $this->projectRepository->findById($command->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        $this->projectUserRepository->transaction(function () use ($project, $command) {
            // Index current assignments by user ID
            $currentAssignments = [];
            foreach ($project->getProjectUsers() as $assignment) {
                $currentAssignments[$assignment->getAppUser()->getId()->toRfc4122()] = $assignment;
            }

            foreach ($command->users as $data) {
                $userId = $data['user_id'];

                $role = $this->roleRepository->findById($data['role_id']);
                if (!$role) {
                    throw new \DomainException("Role with ID {$data['role_id']} not found");
                }

                if (isset($currentAssignments[$userId])) {
                    // Update existing
                    $currentAssignments[$userId]->update(
                        $role,
                        (bool) ($data['is_active'] ?? true)
                    );
                    unset($currentAssignments[$userId]);
                } else {
                    // Create new
                    $user = $this->userRepository->findById($userId);
                    if (!$user) {
                        throw new \DomainException("User with ID {$userId} not found");
                    }

                    $assignment = ProjectUser::create(
                        $project,
                        $user,
                        $role,
                        (bool) ($data['is_active'] ?? true)
                    );

                    $this->projectUserRepository->save($assignment, false);
                }
            }

            // Remove remaining (not in the sync list)
            foreach ($currentAssignments as $oldAssignment) {
                $this->projectUserRepository->remove($oldAssignment);
            }
        });
    }
}
