<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Link;

use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\VO\Link\LinkId;
use DateTimeImmutable;

final readonly class LinkWasUpdated
{
    public function __construct(
        public LinkId $id,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(Link $link): self
    {
        return new self(
            id: $link->id(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
