<?php

namespace App\TimeManagement\Application\DeleteTimeEntry;

final readonly class DeleteTimeEntryCommand
{
    public function __construct(
        public string $timeEntryId,
    ) {}
}
