<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\TimeEntry;

use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use Symfony\Component\Uid\Uuid;

final class TimeEntryMother
{
    public static function create(
        ?TimeEntryId $id = null,
        ?ProjectUserId $projectUserId = null,
        ?TimeEntryDate $date = null,
        ?TimeEntryHours $hours = null,
    ): TimeEntry {
        return TimeEntry::create(
            id: $id ?? TimeEntryIdMother::create(),
            projectUserId: $projectUserId ?? new ProjectUserId(Uuid::v4()->toRfc4122()),
            date: $date ?? TimeEntryDateMother::create(),
            hours: $hours ?? TimeEntryHoursMother::create(),
            description: null,
        );
    }
}
