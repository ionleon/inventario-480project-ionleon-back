<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectRole\CreateProjectRole;

use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;

interface CreateProjectRoleServiceInterface
{
    public function __invoke(ProjectRoleId $id, ProjectRoleName $name): ProjectRole;
}
