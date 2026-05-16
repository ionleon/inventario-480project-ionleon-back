<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\TimeEntry;

use App\Core\Domain\Model\Event\TimeEntry\TimeEntryWasCreated;
use App\Core\Domain\Model\Event\TimeEntry\TimeEntryWasDeleted;
use App\Core\Domain\Model\Event\TimeEntry\TimeEntryWasUpdated;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryDateMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryHoursMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryMother;
use PHPUnit\Framework\TestCase;

final class TimeEntryAggregateTest extends TestCase
{
    public function test_GivenValidData_WhenCreate_ThenEventRecorded(): void
    {
        $timeEntry = TimeEntryMother::create();

        $events = $timeEntry->pullEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(TimeEntryWasCreated::class, $events[0]);
    }

    public function test_GivenExistingEntry_WhenUpdate_ThenEventRecorded(): void
    {
        $timeEntry = TimeEntryMother::create();
        $timeEntry->pullEvents(); // clear create event

        $timeEntry->update(
            TimeEntryDateMother::create('2026-06-01'),
            TimeEntryHoursMother::create(4.0),
            null,
        );

        $events = $timeEntry->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(TimeEntryWasUpdated::class, $events[0]);
    }

    public function test_GivenExistingEntry_WhenDelete_ThenEventRecorded(): void
    {
        $timeEntry = TimeEntryMother::create();
        $timeEntry->pullEvents(); // clear create event

        $timeEntry->delete();

        $events = $timeEntry->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(TimeEntryWasDeleted::class, $events[0]);
    }

    public function test_GivenExistingEntry_WhenUpdate_ThenHoursChanged(): void
    {
        $timeEntry = TimeEntryMother::create(hours: TimeEntryHoursMother::create(8.0));
        $timeEntry->pullEvents();

        $newHours = TimeEntryHoursMother::create(3.5);
        $timeEntry->update(TimeEntryDateMother::create(), $newHours, null);

        $this->assertSame('3.50', $timeEntry->hours()->value());
    }
}
