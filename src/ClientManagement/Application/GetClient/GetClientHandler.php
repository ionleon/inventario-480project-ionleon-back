<?php

namespace App\ClientManagement\Application\GetClient;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;

final class GetClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
    ) {}

    public function handle(GetClientQuery $query): Client
    {
        $client = $this->clientRepository->findById($query->clientId);

        if (!$client) {
            throw new \DomainException('Client not found');
        }

        return $client;
    }
}
