<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\TimeEntry;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;

final class TimeEntryDateMother
{
    public static function create(?string $date = null): TimeEntryDate
    {
        return new TimeEntryDate($date ?? '2026-01-15');
    }
}
