<?php

namespace App\ProjectManagement\Application\GetProject;

final readonly class GetProjectQuery
{
    public function __construct(
        public string $projectId,
    ) {}
}
