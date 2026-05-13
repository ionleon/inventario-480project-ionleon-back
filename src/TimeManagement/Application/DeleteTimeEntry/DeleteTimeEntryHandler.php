<?php

namespace App\TimeManagement\Application\DeleteTimeEntry;

use App\TimeManagement\Domain\TimeEntryRepositoryInterface;

final class DeleteTimeEntryHandler
{
    public function __construct(
        private readonly TimeEntryRepositoryInterface $repository,
    ) {}

    public function handle(DeleteTimeEntryCommand $command): void
    {
        $timeEntry = $this->repository->findById($command->timeEntryId);

        if (!$timeEntry) {
            throw new \DomainException('Time entry not found');
        }

        $this->repository->delete($timeEntry);
    }
}
