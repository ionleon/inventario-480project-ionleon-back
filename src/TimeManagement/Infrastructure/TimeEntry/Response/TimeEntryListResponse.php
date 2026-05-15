<?php

namespace App\TimeManagement\Infrastructure\TimeEntry\Response;

use App\Shared\Domain\Pagination\PaginatedResult;
use App\TimeManagement\Domain\TimeEntry;

final readonly class TimeEntryListResponse
{
    public array $items;
    public int $total;
    public int $page;
    public int $perPage;
    public int $totalPages;

    private function __construct(PaginatedResult $result)
    {
        $this->items = array_map(
            fn(TimeEntry $entry) => TimeEntryResponse::fromEntity($entry),
            iterator_to_array($result->items)
        );
        $this->total = $result->totalItems;
        $this->page = $result->currentPage;
        $this->perPage = $result->itemsPerPage;
        $this->totalPages = $result->getTotalPages();
    }

    public static function fromPaginatedResult(PaginatedResult $result): self
    {
        return new self($result);
    }
}
