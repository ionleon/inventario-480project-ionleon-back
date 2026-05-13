<?php

namespace App\TimeManagement\Application\UpdateTimeEntry;

use App\TimeManagement\Domain\TimeEntry;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;

final class UpdateTimeEntryHandler
{
    public function __construct(
        private readonly TimeEntryRepositoryInterface $repository,
    ) {}

    public function handle(UpdateTimeEntryCommand $command): TimeEntry
    {
        $timeEntry = $this->repository->findById($command->timeEntryId);

        if (!$timeEntry) {
            throw new \DomainException('Time entry not found');
        }

        if ($command->date !== null) {
            try {
                $timeEntry->setDate(new \DateTime($command->date));
            } catch (\Exception) {
                throw new \InvalidArgumentException('Date format invalid. Use YYYY-MM-DD.');
            }
        }

        if ($command->hour !== null) {
            $timeEntry->setHour((string) $command->hour);
        }

        if ($command->comment !== null) {
            $timeEntry->setComment($command->comment);
        }

        $this->repository->save($timeEntry);

        return $timeEntry;
    }
}
