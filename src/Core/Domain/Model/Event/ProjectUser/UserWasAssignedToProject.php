<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\ProjectUser;

use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use DateTimeImmutable;

final readonly class UserWasAssignedToProject
{
    public function __construct(
        public ProjectUserId $id,
        public ProjectId $projectId,
        public UserId $userId,
        public ProjectRoleId $roleId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public static function from(\App\Core\Domain\Model\Aggregate\ProjectUser $projectUser): self
    {
        return new self(
            id: $projectUser->id(),
            projectId: $projectUser->projectId(),
            userId: $projectUser->userId(),
            roleId: $projectUser->roleId(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
