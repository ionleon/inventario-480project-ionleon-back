<?php

namespace App\ClientManagement\Domain\Client;

use App\Shared\Domain\Pagination\PaginatedResult;

interface ClientRepositoryInterface
{
    #Need to pass ClientFilter item through parameter
    public function findWithSectorsPaginated(ClientFilters $filters, int $page, int $limit): PaginatedResult;

    public function findById(string $id): ?Client;

    public function save(Client $client): void;

    public function delete(Client $client): void;
}
