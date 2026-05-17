<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\ListProjects;

use App\App\UI\API\Controller\Project\GetProject\GetProjectResponse;
use App\Core\Domain\Model\Aggregate\Project;
use App\Shared\Domain\Pagination\PaginatedResult;

final readonly class ListProjectsResponse
{
    /** @param list<GetProjectResponse> $items */
    public function __construct(
        public array $items,
        public int $totalItems,
        public int $currentPage,
        public int $itemsPerPage,
        public int $totalPages,
    ) {
    }

    public static function from(PaginatedResult $result): self
    {
        $items = array_map(
            static fn(Project $project) => GetProjectResponse::from($project),
            array_values(array_filter((array) $result->items, static fn(mixed $item) => $item instanceof Project)),
        );

        return new self(
            items: $items,
            totalItems: $result->totalItems,
            currentPage: $result->currentPage,
            itemsPerPage: $result->itemsPerPage,
            totalPages: $result->getTotalPages(),
        );
    }
}
