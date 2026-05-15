<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Project;

use App\Core\Domain\Model\VO\Project\ProjectId;
use DateTimeImmutable;

final readonly class ProjectWasUpdated
{
    public function __construct(
        public ProjectId $id,
        public DateTimeImmutable $occurredAt,
    ) {}

    public static function from(ProjectId $id): self
    {
        return new self(
            id: $id,
            occurredAt: new DateTimeImmutable(),
        );
    }
}
