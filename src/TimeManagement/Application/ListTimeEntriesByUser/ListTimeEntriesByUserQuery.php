<?php

namespace App\TimeManagement\Application\ListTimeEntriesByUser;

final readonly class ListTimeEntriesByUserQuery
{
    public function __construct(
        public string $userId,
        public int $page = 1,
        public int $limit = 10,
    ) {}
}
