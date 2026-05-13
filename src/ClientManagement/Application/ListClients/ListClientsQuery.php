<?php

namespace App\ClientManagement\Application\ListClients;

final readonly class ListClientsQuery
{
    public function __construct(
        public ?string $term,
        public ?bool $isActive,
        public int $page,
        public int $limit,
    ) {}
}
