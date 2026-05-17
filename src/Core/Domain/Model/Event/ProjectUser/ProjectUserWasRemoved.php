<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\ProjectUser;

use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use DateTimeImmutable;

final readonly class ProjectUserWasRemoved
{
    public function __construct(
        public ProjectUserId $id,
        public ProjectId $projectId,
        public UserId $userId,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
