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

        if ($command->date !== null || $command->hour !== null) {
            try {
                $date = $command->date !== null
                    ? new \DateTime($command->date)
                    : $timeEntry->getDate();
                $hour = $command->hour !== null
                    ? (string) $command->hour
                    : $timeEntry->getHour();
                $timeEntry->updateTime($date, $hour);
            } catch (\Exception) {
                throw new \InvalidArgumentException('Date format invalid. Use YYYY-MM-DD.');
            }
        }

        if ($command->comment !== null) {
            $timeEntry->updateComment($command->comment);
        }

        $this->repository->save($timeEntry);

        return $timeEntry;
    }
}
