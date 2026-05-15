<?php

namespace App\Shared\Domain\Pagination;



#Not used, might use in the future
class PaginatedResult
{
    public function __construct(
        /** @var iterable<mixed> */
        public iterable $items,
        public int $totalItems,
        public int $currentPage,
        public int $itemsPerPage,
    ) {}

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }
}
