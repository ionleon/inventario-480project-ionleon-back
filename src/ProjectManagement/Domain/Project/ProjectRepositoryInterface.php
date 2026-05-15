<?php

namespace App\ProjectManagement\Domain\Project;

use App\Shared\Domain\Pagination\PaginatedResult;

interface ProjectRepositoryInterface
{

    public function findById(string $id): ?Project;
    public function findByFilters(ProjectFilters $filters) : array;
    public function findByClientPaginated(string $clientId, int $page, int $limit): PaginatedResult;
    public function findByUserPaginated(string $userId, int $page, int $limit): PaginatedResult;
    public function save(Project $project): void;

    public function findByFiltersPaginated(
        ProjectFilters $filters,
        int $page,
        int $limit
    ): PaginatedResult;

    public function delete(Project $project): void;

}
