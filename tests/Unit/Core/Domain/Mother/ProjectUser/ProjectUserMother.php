<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\ProjectUser;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;

final class ProjectUserMother
{
    public static function create(
        ?ProjectUserId $id = null,
        ?ProjectId $projectId = null,
        ?UserId $userId = null,
        ?ProjectRoleId $roleId = null,
        ?ProjectUserAllocation $allocation = null,
    ): ProjectUser {
        return ProjectUser::assign(
            id: $id ?? ProjectUserIdMother::create(),
            projectId: $projectId ?? ProjectIdMother::create(),
            userId: $userId ?? new UserId(\Symfony\Component\Uid\Uuid::v4()->toRfc4122()),
            roleId: $roleId ?? ProjectRoleIdMother::create(),
            allocation: $allocation ?? ProjectUserAllocationMother::create(),
        );
    }
}
