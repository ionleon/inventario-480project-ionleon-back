<?php

namespace App\ClientManagement\Application;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;
use Exception;
use Symfony\Component\Uid\Uuid;

class ClientService
{


    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private SectorRepositoryInterface $sectorRepository
    ) {}

    public function create(array $data): Client
    {
        if (!isset($data['id'], $data['name'], $data['sector_id'])) {
            throw new \InvalidArgumentException('ID, Name and Sector are mandatory for new clients');
        }

        $client = new Client();
        $client->setId(Uuid::fromString($data['id']));

        return $this->update($client, $data);
    }

    /**
     * @throws Exception
     */
    public function update(Client $client, array $data): Client
    {

        if (isset($data['name'])) {
            $client->setName($data['name']);
        }

        if (isset($data['is_active'])) {
            $client->setIsActive((bool)$data['is_active']);
        }

        if (isset($data['sector_id'])) {
            $sector = $this->sectorRepository->findById($data['sector_id']);
            if (!$sector) {
                throw new Exception('Sector not found');
            }
            $client->setSector($sector);
        }

        if (null === $client->getSector()) {
            throw new \LogicException('A client must have a sector');
        }

        $this->clientRepository->save($client);

        return $client;
    }

    public function delete(Client $client): void
    {
        if (!$client->getProjects()->isEmpty()) {
            throw new \LogicException('Cannot delete client with associeted project');
        }

        $this->clientRepository->delete($client);
    }

    public function setActivation(Client $client,bool $isActive): void
    {
        $client->setIsActive($isActive);
        $this->clientRepository->save($client);
    }
}
