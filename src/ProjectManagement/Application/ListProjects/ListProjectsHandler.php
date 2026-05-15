<?php

namespace App\ProjectManagement\Application\ListProjects;

use App\ProjectManagement\Domain\Project\ProjectFilters;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;

final class ListProjectsHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(ListProjectsQuery $query): PaginatedResult
    {
        $filters = new ProjectFilters(
            term: $query->term,
            clientId: $query->clientId,
            isActive: $query->isActive,
        );

        return $this->projectRepository->findByFiltersPaginated($filters, $query->page, $query->limit);
    }
}
