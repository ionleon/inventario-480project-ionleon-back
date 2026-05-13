<?php

namespace App\ClientManagement\Application\CreateSector;

use App\ClientManagement\Domain\Sector\Sector;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateSectorHandler
{
    public function __construct(
        private readonly SectorRepositoryInterface $sectorRepository,
    ) {}

    public function handle(CreateSectorCommand $command): Sector
    {
        $sector = new Sector();
        $sector->setId(Uuid::fromString($command->id));
        $sector->setName($command->name);

        $this->sectorRepository->save($sector);

        return $sector;
    }
}
