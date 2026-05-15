<?php

namespace App\ProjectManagement\Application\DeleteProject;

final readonly class DeleteProjectCommand
{
    public function __construct(
        public string $projectId,
    ) {}
}
