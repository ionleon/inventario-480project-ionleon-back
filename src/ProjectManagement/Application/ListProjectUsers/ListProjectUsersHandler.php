<?php

namespace App\ProjectManagement\Application\ListProjectUsers;

use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;

final class ListProjectUsersHandler
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $repository,
    ) {}

    public function handle(ListProjectUsersQuery $query): array
    {
        $paginatedResult = $this->repository->findByProjectPaginated(
            $query->projectId,
            $query->page,
            $query->limit
        );

        return array_map(
            fn($assignment) => ProjectUserDTO::fromEntity($assignment)->toArray(),
            $paginatedResult->items
        );
    }
}
