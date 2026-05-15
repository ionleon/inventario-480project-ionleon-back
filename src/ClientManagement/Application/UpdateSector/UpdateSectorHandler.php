<?php

namespace App\ClientManagement\Application\UpdateSector;

use App\ClientManagement\Domain\Sector\Sector;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;

final class UpdateSectorHandler
{
    public function __construct(
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(UpdateSectorCommand $command): Sector
    {
        $sector = $this->sectorRepository->findById($command->sectorId);

        if (!$sector) {
            throw new \DomainException('Sector not found');
        }

        if ($command->name !== null) {
            $sector->setName($command->name);
        }

        $this->sectorRepository->save($sector);

        return $sector;
    }
}
