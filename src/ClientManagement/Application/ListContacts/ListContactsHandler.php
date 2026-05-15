<?php

namespace App\ClientManagement\Application\ListContacts;

use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;

final class ListContactsHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ContactRepositoryInterface $contactRepository,
    ) {}

    public function handle(ListContactsQuery $query): PaginatedResult
    {
        $client = $this->clientRepository->findById($query->clientId);

        if (!$client) {
            throw new \DomainException('Client not found');
        }

        return $this->contactRepository->findByClientPaginated($client, $query->page, $query->limit);
    }
}
