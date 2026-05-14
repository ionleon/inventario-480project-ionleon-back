<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Sector\CreateSector;

use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;

interface CreateSectorServiceInterface
{
    public function __invoke(SectorId $id, SectorName $name): Sector;
}
