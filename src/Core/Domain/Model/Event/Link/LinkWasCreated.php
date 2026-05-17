<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Link;

use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Project\ProjectId;
use DateTimeImmutable;

final readonly class LinkWasCreated
{
    public function __construct(
        public LinkId $id,
        public ProjectId $projectId,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(Link $link): self
    {
        return new self(
            id: $link->id(),
            projectId: $link->projectId(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
