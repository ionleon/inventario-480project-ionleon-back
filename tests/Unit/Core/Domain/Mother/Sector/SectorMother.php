<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Sector;

use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;

final class SectorMother
{
    public static function create(
        ?SectorId $id = null,
        ?SectorName $name = null,
    ): Sector {
        return Sector::create(
            id: $id ?? SectorIdMother::create(),
            name: $name ?? SectorNameMother::create(),
        );
    }
}
