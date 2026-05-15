<?php

namespace App\ProjectManagement\Application\UpdateProjectUser;

use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;

final class UpdateProjectUserHandler
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
        private readonly ProjectRoleRepositoryInterface $roleRepository,
    ) {}

    public function handle(UpdateProjectUserCommand $command): ProjectUser
    {
        // Find assignment
        $assignment = $this->projectUserRepository->findOneByProjectAndUser(
            $command->projectId,
            $command->userId
        );

        if (!$assignment) {
            throw new \DomainException('Assignment not found');
        }

        // Get role
        $role = $command->roleId
            ? $this->roleRepository->findById($command->roleId)
            : $assignment->getProjectRole();

        if ($command->roleId && !$role) {
            throw new \DomainException('Role not found.');
        }

        $isActive = $command->isActive ?? $assignment->isActive();

        $assignment->update($role, $isActive);

        $this->projectUserRepository->save($assignment, true);

        return $assignment;
    }
}
