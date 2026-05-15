<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\ProjectRole;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;

final class ProjectRoleIdMother
{
    public static function create(?string $value = null): ProjectRoleId
    {
        return new ProjectRoleId($value ?? ProjectRoleId::generate()->__toString());
    }
}
