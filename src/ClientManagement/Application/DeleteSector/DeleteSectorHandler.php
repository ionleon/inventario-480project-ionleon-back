<?php

namespace App\ClientManagement\Application\DeleteSector;

use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;

final class DeleteSectorHandler
{
    public function __construct(
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(DeleteSectorCommand $command): void
    {
        $sector = $this->sectorRepository->findById($command->sectorId);

        if (!$sector) {
            throw new \DomainException('Sector not found');
        }

        if (!$sector->getClients()->isEmpty()) {
            throw new \LogicException('Can\'t delete sectors in use.');
        }

        $this->sectorRepository->delete($sector);
    }
}
