<?php

namespace App\ProjectManagement\Application\ListProjectUsers;

final readonly class ListProjectUsersQuery
{
    public function __construct(
        public string $projectId,
        public int $page = 1,
        public int $limit = 10,
    ) {}
}
