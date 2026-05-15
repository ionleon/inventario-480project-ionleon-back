<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\ProjectRole;

use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;

final class ProjectRoleMother
{
    public static function create(
        ?ProjectRoleId $id = null,
        ?ProjectRoleName $name = null,
    ): ProjectRole {
        return ProjectRole::create(
            id: $id ?? ProjectRoleIdMother::create(),
            name: $name ?? ProjectRoleNameMother::create(),
        );
    }
}
