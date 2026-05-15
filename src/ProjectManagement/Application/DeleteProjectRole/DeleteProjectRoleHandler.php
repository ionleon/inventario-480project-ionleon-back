<?php

namespace App\ProjectManagement\Application\DeleteProjectRole;

use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;

final class DeleteProjectRoleHandler
{
    public function __construct(
        private readonly ProjectRoleRepositoryInterface $repository,
    ) {}

    public function handle(DeleteProjectRoleCommand $command): void
    {
        $role = $this->repository->findById($command->roleId);

        if (!$role) {
            throw new \DomainException('Project role not found');
        }

        $this->repository->delete($role);
    }
}
