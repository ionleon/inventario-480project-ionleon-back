<?php

namespace App\ClientManagement\Application\DeleteClient;

use App\ClientManagement\Domain\Client\ClientRepositoryInterface;

final class DeleteClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
    ) {}

    public function handle(DeleteClientCommand $command): void
    {
        $client = $this->clientRepository->findById($command->clientId);

        if (!$client) {
            throw new \DomainException('Client not found');
        }

        if (!$client->getProjects()->isEmpty()) {
            throw new \LogicException('Cannot delete client with associated project');
        }

        $this->clientRepository->delete($client);
    }
}
