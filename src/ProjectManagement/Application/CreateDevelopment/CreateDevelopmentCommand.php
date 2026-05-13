<?php

namespace App\ProjectManagement\Application\CreateDevelopment;

final readonly class CreateDevelopmentCommand
{
    public function __construct(
        public string $projectId,
        public string $id,
        public string $technologyId,
        public string $name,
        public string $description,
        public string $urlRepository,
        public ?array $links,
    ) {}
}
