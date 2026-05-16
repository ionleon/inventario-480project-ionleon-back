<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\TimeEntry;

use App\Core\Domain\Exception\VO\InvalidTimeEntryDateException;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use PHPUnit\Framework\TestCase;

final class TimeEntryDateTest extends TestCase
{
    public function test_GivenValidDateString_WhenCreated_ThenSuccess(): void
    {
        $date = new TimeEntryDate('2026-05-15');
        $this->assertSame('2026-05-15', (string) $date);
    }

    public function test_GivenInvalidDateString_WhenCreated_ThenThrows(): void
    {
        $this->expectException(InvalidTimeEntryDateException::class);
        new TimeEntryDate('not-a-date');
    }

    public function test_GivenDateTimeImmutable_WhenCreated_ThenSuccess(): void
    {
        $dt = new \DateTimeImmutable('2026-05-15');
        $date = new TimeEntryDate($dt);
        $this->assertSame('2026-05-15', (string) $date);
    }

    public function test_TimeIsTruncatedToMidnight(): void
    {
        $dt = new \DateTimeImmutable('2026-05-15 14:30:00');
        $date = new TimeEntryDate($dt);
        $this->assertSame('00:00:00', $date->value()->format('H:i:s'));
    }
}
