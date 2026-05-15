<?php

namespace App\ClientManagement\Application\GetSector;

use App\ClientManagement\Domain\Sector\Sector;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;

final class GetSectorHandler
{
    public function __construct(
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(GetSectorQuery $query): Sector
    {
        $sector = $this->sectorRepository->findById($query->sectorId);

        if (!$sector) {
            throw new \DomainException('Sector not found');
        }

        return $sector;
    }
}
