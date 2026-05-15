<?php

namespace App\ProjectManagement\Application\ListDevelopments;

final readonly class ListDevelopmentsQuery
{
    public function __construct(
        public string $projectId,
    ) {}
}
