<?php

namespace App\ProjectManagement\Application\ListProjectsByClient;

final readonly class ListProjectsByClientQuery
{
    public function __construct(
        public string $clientId,
        public int $page,
        public int $limit,
    ) {}
}
