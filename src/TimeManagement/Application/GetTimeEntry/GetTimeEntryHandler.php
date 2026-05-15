<?php

namespace App\TimeManagement\Application\GetTimeEntry;

use App\TimeManagement\Domain\TimeEntry;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;

final class GetTimeEntryHandler
{
    public function __construct(
        private readonly TimeEntryRepositoryInterface $repository,
    ) {}

    public function handle(GetTimeEntryQuery $query): TimeEntry
    {
        $timeEntry = $this->repository->findById($query->timeEntryId);

        if (!$timeEntry) {
            throw new \DomainException('Time entry not found');
        }

        return $timeEntry;
    }
}
