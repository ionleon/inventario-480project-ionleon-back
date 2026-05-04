<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class PaginationDto
{
    public function __construct(
        #[Groups(['pagination'])]
        public iterable $items,

        #[Groups(['pagination'])]
        public int $totalItems,

        #[Groups(['pagination'])]
        public int $currentPage,

        #[Groups(['pagination'])]
        public int $itemsPerPage,

        #[Groups(['pagination'])]
        public int $totalPages
    ) {}
}
