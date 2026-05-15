<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\ListClients;

use App\App\UI\API\Controller\Client\GetClient\GetClientResponse;
use App\Core\Domain\Model\Aggregate\Client;
use App\Shared\Domain\Pagination\PaginatedResult;

final readonly class ListClientsResponse
{
    /** @param list<GetClientResponse> $items */
    public function __construct(
        public array $items,
        public int $totalItems,
        public int $currentPage,
        public int $itemsPerPage,
        public int $totalPages,
    ) {}

    public static function from(PaginatedResult $result): self
    {
        $items = array_map(
            static fn(Client $client) => GetClientResponse::from($client),
            array_values(array_filter((array) $result->items, static fn(mixed $item) => $item instanceof Client)),
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
