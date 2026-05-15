<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\UpdateProjectUser;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;

interface UpdateProjectUserServiceInterface
{
    public function __invoke(
        ProjectUserId $id,
        ProjectRoleId $roleId,
        ProjectUserAllocation $allocation,
    ): ProjectUser;
}
