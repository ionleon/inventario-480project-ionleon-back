<?php

namespace App\TimeManagement\Application\GetTimeEntry;

final readonly class GetTimeEntryQuery
{
    public function __construct(
        public string $timeEntryId,
    ) {}
}
