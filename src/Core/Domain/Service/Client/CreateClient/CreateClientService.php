<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Client\CreateClient;

use App\Core\Domain\Exception\Client\DuplicatedClientNameException;
use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;

final readonly class CreateClientService implements CreateClientServiceInterface
{
    public function __construct(
        private ClientRepository $clientRepository,
        private SectorRepository $sectorRepository,
    ) {}

    /**
     * @throws DuplicatedClientNameException
     * @throws SectorNotFoundException
     */
    public function __invoke(ClientId $id, ClientName $name, SectorId $sectorId): Client
    {
        if ($this->clientRepository->findOneByName($name) !== null) {
            throw new DuplicatedClientNameException((string) $name);
        }

        $this->sectorRepository->findOneOrFail($sectorId);

        $client = Client::create(id: $id, name: $name, sectorId: $sectorId);
        $this->clientRepository->add($client);

        return $client;
    }
}
