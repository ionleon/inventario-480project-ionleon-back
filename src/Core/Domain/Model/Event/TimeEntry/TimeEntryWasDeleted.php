<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\TimeEntry;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use DateTimeImmutable;

final readonly class TimeEntryWasDeleted
{
    public function __construct(
        public TimeEntryId $id,
        public DateTimeImmutable $occurredAt,
    ) {}
}
