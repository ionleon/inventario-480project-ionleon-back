<?php

namespace App\ClientManagement\Application\CreateClient;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateClientHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(CreateClientCommand $command): Client
    {
        $sector = $this->sectorRepository->findById($command->sectorId);

        if (!$sector) {
            throw new \DomainException('Sector not found');
        }

        $client = new Client();
        $client->setId(Uuid::fromString($command->id));
        $client->setName($command->name);
        $client->setSector($sector);
        $client->setIsActive(true);

        $this->clientRepository->save($client);

        return $client;
    }
}
