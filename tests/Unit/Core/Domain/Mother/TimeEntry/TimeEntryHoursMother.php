<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\TimeEntry;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;

final class TimeEntryHoursMother
{
    public static function create(float $value = 8.0): TimeEntryHours
    {
        return new TimeEntryHours($value);
    }
}
