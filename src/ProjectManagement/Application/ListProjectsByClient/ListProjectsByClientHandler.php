<?php

namespace App\ProjectManagement\Application\ListProjectsByClient;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;

final class ListProjectsByClientHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(ListProjectsByClientQuery $query): PaginatedResult
    {
        return $this->projectRepository->findByClientPaginated($query->clientId, $query->page, $query->limit);
    }
}
