<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Sector;

use App\Core\Domain\Model\VO\Sector\SectorId;

final class SectorIdMother
{
    public static function create(?string $value = null): SectorId
    {
        return new SectorId($value ?? SectorId::generate()->__toString());
    }
}
