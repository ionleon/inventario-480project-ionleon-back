<?php

namespace App\ClientManagement\Application\UpdateClient;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;

final class UpdateClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(UpdateClientCommand $command): Client
    {
        $client = $this->clientRepository->findById($command->clientId);

        if (!$client) {
            throw new \DomainException('Client not found');
        }

        if ($command->name !== null) {
            $client->setName($command->name);
        }

        if ($command->isActive !== null) {
            $client->setIsActive($command->isActive);
        }

        if ($command->sectorId !== null) {
            $sector = $this->sectorRepository->findById($command->sectorId);

            if (!$sector) {
                throw new \DomainException('Sector not found');
            }

            $client->setSector($sector);
        }

        if ($client->getSector() === null) {
            throw new \LogicException('A client must have a sector');
        }

        $this->clientRepository->save($client);

        return $client;
    }
}
