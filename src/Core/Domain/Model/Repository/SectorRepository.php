<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;

interface SectorRepository
{
    public function add(Sector $sector): void;

    public function remove(Sector $sector): void;

    public function find(SectorId $id): ?Sector;

    /** @throws SectorNotFoundException */
    public function findOneOrFail(SectorId $id): Sector;

    public function findOneByName(SectorName $name): ?Sector;

    /** @return list<Sector> */
    public function all(): array;
}
