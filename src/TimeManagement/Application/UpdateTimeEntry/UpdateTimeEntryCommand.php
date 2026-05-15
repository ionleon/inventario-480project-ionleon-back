<?php

namespace App\TimeManagement\Application\UpdateTimeEntry;

final readonly class UpdateTimeEntryCommand
{
    public function __construct(
        public string $timeEntryId,
        public ?string $date = null,
        public ?float $hour = null,
        public ?string $comment = null,
    ) {}
}
