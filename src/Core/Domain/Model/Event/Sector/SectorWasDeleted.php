<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Sector;

use App\Core\Domain\Model\VO\Sector\SectorId;
use DateTimeImmutable;

final readonly class SectorWasDeleted
{
    public function __construct(
        public SectorId $id,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
