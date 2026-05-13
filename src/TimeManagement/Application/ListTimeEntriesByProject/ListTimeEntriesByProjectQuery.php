<?php

namespace App\TimeManagement\Application\ListTimeEntriesByProject;

final readonly class ListTimeEntriesByProjectQuery
{
    public function __construct(
        public string $projectId,
        public ?string $userId = null,
        public int $page = 1,
        public int $limit = 10,
    ) {}
}
