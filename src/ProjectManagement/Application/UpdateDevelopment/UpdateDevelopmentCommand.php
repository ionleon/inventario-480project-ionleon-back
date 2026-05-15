<?php

namespace App\ProjectManagement\Application\UpdateDevelopment;

final readonly class UpdateDevelopmentCommand
{
    public function __construct(
        public string $developmentId,
        public ?string $technologyId,
        public ?string $name,
        public ?string $description,
        public ?string $urlRepository,
        public ?array $links,
    ) {}
}
