<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProjectRole\ListProjectRoles;

use App\App\UI\API\Controller\ProjectRole\ListProjectRoles\ListProjectRolesResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;

final readonly class ListProjectRolesHandler implements QueryHandler
{
    public function __construct(private ProjectRoleRepository $repository) {}

    public function __invoke(ListProjectRolesQuery $query): ListProjectRolesResponse
    {
        return ListProjectRolesResponse::from($this->repository->all());
    }
}
