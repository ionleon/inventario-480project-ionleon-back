<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\TimeEntry\DeleteTimeEntry;

use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;

final readonly class DeleteTimeEntryService implements DeleteTimeEntryServiceInterface
{
    public function __construct(
        private TimeEntryRepository $timeEntryRepository,
    ) {
    }

    public function __invoke(TimeEntryId $id): void
    {
        $timeEntry = $this->timeEntryRepository->findOneOrFail($id);
        $timeEntry->delete();
        $this->timeEntryRepository->remove($timeEntry);
    }
}
