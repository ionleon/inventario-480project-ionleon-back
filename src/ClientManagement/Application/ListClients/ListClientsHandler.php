<?php

namespace App\ClientManagement\Application\ListClients;

use App\ClientManagement\Domain\Client\ClientFilters;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;

final class ListClientsHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
    ) {}

    public function handle(ListClientsQuery $query): PaginatedResult
    {
        $filters = new ClientFilters(
            term: $query->term,
            isActive: $query->isActive,
        );

        return $this->clientRepository->findWithSectorsPaginated($filters, $query->page, $query->limit);
    }
}
