<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\TimeEntry;

use App\Core\Domain\Exception\TimeEntry\TimeEntryNotFoundException;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Service\TimeEntry\UpdateTimeEntry\UpdateTimeEntryService;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryDateMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryHoursMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryIdMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryMother;
use PHPUnit\Framework\TestCase;

final class UpdateTimeEntryServiceTest extends TestCase
{
    public function test_GivenValidId_WhenInvoke_ThenTimeEntryUpdated(): void
    {
        $timeEntry = TimeEntryMother::create(hours: TimeEntryHoursMother::create(8.0));

        $repo = $this->createMock(TimeEntryRepository::class);
        $repo->method('findOneOrFail')->willReturn($timeEntry);

        $service = new UpdateTimeEntryService($repo);

        $result = $service(
            TimeEntryIdMother::create(),
            TimeEntryDateMother::create('2026-06-01'),
            TimeEntryHoursMother::create(4.0),
            null,
        );

        $this->assertSame('4.00', $result->hours()->value());
        $this->assertSame('2026-06-01', (string) $result->date());
    }

    public function test_GivenNonExistentId_WhenInvoke_ThenThrows(): void
    {
        $repo = $this->createMock(TimeEntryRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new TimeEntryNotFoundException());

        $this->expectException(TimeEntryNotFoundException::class);

        (new UpdateTimeEntryService($repo))(
            TimeEntryIdMother::create(),
            TimeEntryDateMother::create(),
            TimeEntryHoursMother::create(),
            null,
        );
    }
}
