<?php

namespace App\ClientManagement\Application\ToggleClientActivation;

use App\ClientManagement\Domain\Client\ClientRepositoryInterface;

final class ToggleClientActivationHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
    ) {}

    public function handle(ToggleClientActivationCommand $command): void
    {
        $client = $this->clientRepository->findById($command->clientId);

        if (!$client) {
            throw new \DomainException('Client not found');
        }

        $client->setIsActive(!$client->isActive());

        $this->clientRepository->save($client);
    }
}
