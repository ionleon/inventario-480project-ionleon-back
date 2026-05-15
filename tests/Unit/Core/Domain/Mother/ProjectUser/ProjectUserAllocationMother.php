<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\ProjectUser;

use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;

final class ProjectUserAllocationMother
{
    public static function create(int $value = 100): ProjectUserAllocation
    {
        return new ProjectUserAllocation($value);
    }
}
