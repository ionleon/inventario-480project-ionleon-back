<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\TimeEntry\UpdateTimeEntry;

use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;

final readonly class UpdateTimeEntryService implements UpdateTimeEntryServiceInterface
{
    public function __construct(
        private TimeEntryRepository $timeEntryRepository,
    ) {
    }

    public function __invoke(
        TimeEntryId $id,
        TimeEntryDate $date,
        TimeEntryHours $hours,
        ?TimeEntryDescription $description,
    ): TimeEntry {
        $timeEntry = $this->timeEntryRepository->findOneOrFail($id);
        $timeEntry->update($date, $hours, $description);

        return $timeEntry;
    }
}
