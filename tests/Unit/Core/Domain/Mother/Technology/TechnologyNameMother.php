<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Technology;

use App\Core\Domain\Model\VO\Technology\TechnologyName;
use Faker\Factory;

final class TechnologyNameMother
{
    public static function create(?string $value = null): TechnologyName
    {
        $faker = Factory::create();
        // words can be 1 char, append suffix to guarantee min-length invariant
        return new TechnologyName($value ?? ($faker->unique()->word() . '-tech'));
    }
}
