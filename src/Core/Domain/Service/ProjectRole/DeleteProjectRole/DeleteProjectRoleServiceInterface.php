<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectRole\DeleteProjectRole;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;

interface DeleteProjectRoleServiceInterface
{
    public function __invoke(ProjectRoleId $id): void;
}
