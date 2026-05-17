<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\TimeEntry;

use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use DateTimeImmutable;

final readonly class TimeEntryWasCreated
{
    public function __construct(
        public TimeEntryId $id,
        public ProjectUserId $projectUserId,
        public TimeEntryDate $date,
        public TimeEntryHours $hours,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
