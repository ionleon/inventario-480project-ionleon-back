<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Sector;

use App\Core\Domain\Model\VO\Sector\SectorName;
use Faker\Factory;

final class SectorNameMother
{
    public static function create(?string $value = null): SectorName
    {
        return new SectorName($value ?? Factory::create()->company());
    }
}
