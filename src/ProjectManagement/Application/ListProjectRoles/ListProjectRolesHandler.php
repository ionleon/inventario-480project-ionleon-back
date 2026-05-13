<?php

namespace App\ProjectManagement\Application\ListProjectRoles;

use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;

final class ListProjectRolesHandler
{
    public function __construct(
        private readonly ProjectRoleRepositoryInterface $repository,
    ) {}

    public function handle(ListProjectRolesQuery $query): array
    {
        return $this->repository->findAll();
    }
}
