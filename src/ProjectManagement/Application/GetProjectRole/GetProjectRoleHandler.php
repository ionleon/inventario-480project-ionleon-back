<?php

namespace App\ProjectManagement\Application\GetProjectRole;

use App\ProjectManagement\Domain\ProjectRole\ProjectRole;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;

final class GetProjectRoleHandler
{
    public function __construct(
        private readonly ProjectRoleRepositoryInterface $repository,
    ) {}

    public function handle(GetProjectRoleQuery $query): ProjectRole
    {
        $role = $this->repository->findById($query->roleId);

        if (!$role) {
            throw new \DomainException('Project role not found');
        }

        return $role;
    }
}
