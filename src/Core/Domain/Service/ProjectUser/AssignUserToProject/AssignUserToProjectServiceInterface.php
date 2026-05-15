<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\AssignUserToProject;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;

interface AssignUserToProjectServiceInterface
{
    public function __invoke(
        ProjectUserId $id,
        ProjectId $projectId,
        UserId $userId,
        ProjectRoleId $roleId,
        ProjectUserAllocation $allocation,
    ): ProjectUser;
}
