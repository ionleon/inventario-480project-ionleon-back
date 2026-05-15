<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\ProjectRole\ProjectRoleNotFoundException;
use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;

interface ProjectRoleRepository
{
    public function add(ProjectRole $projectRole): void;

    public function remove(ProjectRole $projectRole): void;

    public function find(ProjectRoleId $id): ?ProjectRole;

    /** @throws ProjectRoleNotFoundException */
    public function findOneOrFail(ProjectRoleId $id): ProjectRole;

    public function findOneByName(ProjectRoleName $name): ?ProjectRole;

    /** @return list<ProjectRole> */
    public function all(): array;
}
