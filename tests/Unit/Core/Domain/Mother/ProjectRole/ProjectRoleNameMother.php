<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\ProjectRole;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;
use Faker\Factory;

final class ProjectRoleNameMother
{
    public static function create(?string $value = null): ProjectRoleName
    {
        $faker = Factory::create();
        // words can be 1 char, append suffix to guarantee min-length invariant
        return new ProjectRoleName($value ?? ($faker->unique()->word() . '-role'));
    }
}
