<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Sector;

use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;
use DateTimeImmutable;

final readonly class SectorWasCreated
{
    public function __construct(
        public SectorId $id,
        public SectorName $name,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\Sector $sector): self
    {
        return new self(
            id: $sector->id(),
            name: $sector->name(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
