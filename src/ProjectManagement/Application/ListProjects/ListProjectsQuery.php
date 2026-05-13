<?php

namespace App\ProjectManagement\Application\ListProjects;

final readonly class ListProjectsQuery
{
    public function __construct(
        public ?string $term,
        public ?string $clientId,
        public ?bool $isActive,
        public int $page,
        public int $limit,
    ) {}
}
