<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Client\UpdateClient;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Exception\Client\DuplicatedClientNameException;
use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;

final readonly class UpdateClientService implements UpdateClientServiceInterface
{
    public function __construct(
        private ClientRepository $clientRepository,
        private SectorRepository $sectorRepository,
    ) {}

    /**
     * @throws ClientNotFoundException
     * @throws DuplicatedClientNameException
     * @throws SectorNotFoundException
     */
    public function __invoke(ClientId $id, ClientName $name, SectorId $sectorId): void
    {
        $client = $this->clientRepository->findOneOrFail($id);

        $existing = $this->clientRepository->findOneByName($name);
        if ($existing !== null && !$existing->id()->equals($id)) {
            throw new DuplicatedClientNameException((string) $name);
        }

        $this->sectorRepository->findOneOrFail($sectorId);

        $client->update(name: $name, sectorId: $sectorId);
    }
}
