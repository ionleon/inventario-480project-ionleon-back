<?php

namespace App\ProjectManagement\Application\Project;

readonly class ProjectInputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $clientId,
        public ?string $startDate,
        public bool $isActive,
    ) {}
}
