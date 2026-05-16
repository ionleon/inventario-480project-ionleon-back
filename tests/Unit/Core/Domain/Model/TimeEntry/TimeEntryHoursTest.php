<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\TimeEntry;

use App\Core\Domain\Exception\VO\InvalidTimeEntryHoursException;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use PHPUnit\Framework\TestCase;

final class TimeEntryHoursTest extends TestCase
{
    public function test_GivenValidHours_WhenCreated_ThenSuccess(): void
    {
        $hours = new TimeEntryHours(8.5);
        $this->assertSame('8.50', $hours->value());
    }

    public function test_GivenZero_WhenCreated_ThenThrows(): void
    {
        $this->expectException(InvalidTimeEntryHoursException::class);
        new TimeEntryHours(0);
    }

    public function test_GivenNegative_WhenCreated_ThenThrows(): void
    {
        $this->expectException(InvalidTimeEntryHoursException::class);
        new TimeEntryHours(-1);
    }

    public function test_GivenMoreThan24_WhenCreated_ThenThrows(): void
    {
        $this->expectException(InvalidTimeEntryHoursException::class);
        new TimeEntryHours(24.01);
    }

    public function test_GivenExactly24_WhenCreated_ThenSuccess(): void
    {
        $hours = new TimeEntryHours(24.0);
        $this->assertSame('24.00', $hours->value());
    }
}
