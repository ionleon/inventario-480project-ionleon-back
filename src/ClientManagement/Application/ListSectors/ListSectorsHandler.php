<?php

namespace App\ClientManagement\Application\ListSectors;

use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;

final class ListSectorsHandler
{
    public function __construct(
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(ListSectorsQuery $query): array
    {
        return $this->sectorRepository->findAll();
    }
}
