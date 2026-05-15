<?php

namespace App\ClientManagement\Domain\Contact;

use App\ClientManagement\Domain\Client\Client;
use App\Shared\Domain\Pagination\PaginatedResult;

interface ContactRepositoryInterface
{

    public function findById(string $id): ?Contact;

    /** @return Contact[] */
    public function findByClient(Client $client): array;

    public function save(Contact $contact): void;

    public function remove(Contact $contact): void;

    public function resetMainContactsForClient(Client $client, ?Contact $excludeContact = null): void;

    public function countContactsForClient(Client $client): int;

    public function findByClientPaginated(Client $client, int $page, int $limit): PaginatedResult;
}
