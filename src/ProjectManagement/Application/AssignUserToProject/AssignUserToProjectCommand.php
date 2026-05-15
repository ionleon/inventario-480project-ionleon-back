<?php

namespace App\ProjectManagement\Application\AssignUserToProject;

final readonly class AssignUserToProjectCommand
{
    public function __construct(
        public string $projectId,
        public string $userId,
        public string $roleId,
    ) {}
}
