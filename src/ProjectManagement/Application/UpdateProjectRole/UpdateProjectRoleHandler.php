<?php

namespace App\ProjectManagement\Application\UpdateProjectRole;

use App\ProjectManagement\Domain\ProjectRole\ProjectRole;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;

final class UpdateProjectRoleHandler
{
    public function __construct(
        private readonly ProjectRoleRepositoryInterface $repository,
    ) {}

    public function handle(UpdateProjectRoleCommand $command): ProjectRole
    {
        $role = $this->repository->findById($command->roleId);

        if (!$role) {
            throw new \DomainException('Project role not found');
        }

        if ($command->name !== null) {
            $role->setName($command->name);
        }

        $this->repository->save($role);

        return $role;
    }
}
