<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Sector;

use App\Core\Domain\Model\VO\Sector\SectorId;
use DateTimeImmutable;

final readonly class SectorWasUpdated
{
    public function __construct(
        public SectorId $id,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\Sector $sector): self
    {
        return new self(
            id: $sector->id(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
