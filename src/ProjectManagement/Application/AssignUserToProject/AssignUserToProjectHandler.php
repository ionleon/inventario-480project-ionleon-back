<?php

namespace App\ProjectManagement\Application\AssignUserToProject;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\UserManagement\Domain\AppUserRepositoryInterface;

final class AssignUserToProjectHandler
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly ProjectRoleRepositoryInterface $roleRepository,
    ) {}

    public function handle(AssignUserToProjectCommand $command): ProjectUser
    {
        // Check if already assigned
        $existing = $this->projectUserRepository->findOneByProjectAndUser(
            $command->projectId,
            $command->userId
        );

        if ($existing) {
            throw new \DomainException('User already assigned to this project.', 409);
        }

        // Validate project exists
        $project = $this->projectRepository->findById($command->projectId);
        if (!$project) {
            throw new \DomainException('Project not found.');
        }

        // Validate user exists
        $user = $this->userRepository->findById($command->userId);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        // Validate role exists
        $role = $this->roleRepository->findById($command->roleId);
        if (!$role) {
            throw new \DomainException('Role not found.');
        }

        // Create assignment
        $assignment = ProjectUser::create($project, $user, $role, true);

        $this->projectUserRepository->save($assignment, true);

        return $assignment;
    }
}
