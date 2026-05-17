<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\ProjectRole;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;
use DateTimeImmutable;

final readonly class ProjectRoleWasCreated
{
    public function __construct(
        public ProjectRoleId $id,
        public ProjectRoleName $name,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\ProjectRole $projectRole): self
    {
        return new self(
            id: $projectRole->id(),
            name: $projectRole->name(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
