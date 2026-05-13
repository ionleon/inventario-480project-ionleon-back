<?php

namespace App\ProjectManagement\Application\RemoveUserFromProject;

final readonly class RemoveUserFromProjectCommand
{
    public function __construct(
        public string $projectId,
        public string $userId,
    ) {}
}
