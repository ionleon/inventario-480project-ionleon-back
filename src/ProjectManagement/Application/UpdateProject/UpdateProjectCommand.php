<?php

namespace App\ProjectManagement\Application\UpdateProject;

final readonly class UpdateProjectCommand
{
    public function __construct(
        public string $projectId,
        public string $name,
        public string $description,
        public string $clientId,
        public ?string $startDate,
        public bool $isActive,
    ) {}
}
