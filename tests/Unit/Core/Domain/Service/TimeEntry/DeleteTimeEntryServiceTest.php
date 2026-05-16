<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\TimeEntry;

use App\Core\Domain\Exception\TimeEntry\TimeEntryNotFoundException;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Service\TimeEntry\DeleteTimeEntry\DeleteTimeEntryService;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryIdMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryMother;
use PHPUnit\Framework\TestCase;

final class DeleteTimeEntryServiceTest extends TestCase
{
    public function test_GivenValidId_WhenInvoke_ThenTimeEntryRemoved(): void
    {
        $timeEntry = TimeEntryMother::create();

        $repo = $this->createMock(TimeEntryRepository::class);
        $repo->method('findOneOrFail')->willReturn($timeEntry);
        $repo->expects(self::once())->method('remove')->with($timeEntry);

        (new DeleteTimeEntryService($repo))(TimeEntryIdMother::create());
    }

    public function test_GivenNonExistentId_WhenInvoke_ThenThrows(): void
    {
        $repo = $this->createMock(TimeEntryRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new TimeEntryNotFoundException());

        $this->expectException(TimeEntryNotFoundException::class);

        (new DeleteTimeEntryService($repo))(TimeEntryIdMother::create());
    }
}
