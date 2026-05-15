<?php

namespace App\ProjectManagement\Application\CreateProject;

final readonly class CreateProjectCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $clientId,
        public ?string $startDate,
    ) {}
}
