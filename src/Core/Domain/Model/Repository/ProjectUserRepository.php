<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;

interface ProjectUserRepository
{
    public function add(ProjectUser $projectUser): void;

    public function remove(ProjectUser $projectUser): void;

    public function find(ProjectUserId $id): ?ProjectUser;

    /** @throws ProjectUserNotFoundException */
    public function findOneOrFail(ProjectUserId $id): ProjectUser;

    /** @return list<ProjectUser> */
    public function findByProject(ProjectId $projectId): array;

    /** @return list<ProjectUser> */
    public function findActiveByUser(UserId $userId): array;

    public function findOneByProjectAndUser(ProjectId $projectId, UserId $userId): ?ProjectUser;
}
