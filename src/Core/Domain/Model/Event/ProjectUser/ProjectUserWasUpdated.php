<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\ProjectUser;

use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use DateTimeImmutable;

final readonly class ProjectUserWasUpdated
{
    public function __construct(
        public ProjectUserId $id,
        public DateTimeImmutable $occurredAt,
    ) {}
}
