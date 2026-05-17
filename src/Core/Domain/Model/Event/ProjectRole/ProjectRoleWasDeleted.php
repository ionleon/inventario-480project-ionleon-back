<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\ProjectRole;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use DateTimeImmutable;

final readonly class ProjectRoleWasDeleted
{
    public function __construct(
        public ProjectRoleId $id,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\ProjectRole $projectRole): self
    {
        return new self(
            id: $projectRole->id(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
