<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\GetTimeEntry;

use App\Core\Domain\Model\Aggregate\TimeEntry;

final readonly class TimeEntryResponse
{
    public function __construct(
        public string $id,
        public string $projectUserId,
        public string $date,
        public string $hours,
        public ?string $description,
    ) {
    }

    public static function from(TimeEntry $timeEntry): self
    {
        return new self(
            id: (string) $timeEntry->id(),
            projectUserId: (string) $timeEntry->projectUserId(),
            date: (string) $timeEntry->date(),
            hours: (string) $timeEntry->hours(),
            description: $timeEntry->description()?->value(),
        );
    }
}
