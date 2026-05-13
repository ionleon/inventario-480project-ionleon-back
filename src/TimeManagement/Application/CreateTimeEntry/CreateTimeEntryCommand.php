<?php

namespace App\TimeManagement\Application\CreateTimeEntry;

final readonly class CreateTimeEntryCommand
{
    public function __construct(
        public string $id,
        public string $date,
        public float $hour,
        public ?string $comment,
        public ?string $projectUserId,
        public ?string $projectId,
        public ?string $userId,
    ) {}
}
