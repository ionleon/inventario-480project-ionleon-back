<?php

namespace App\ProjectManagement\Application;

readonly class ProjectInputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public int $clientId,
        public ?string $startDate,
        public bool $isActive,
    ) {}
}
