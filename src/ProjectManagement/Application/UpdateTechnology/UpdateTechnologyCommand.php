<?php

namespace App\ProjectManagement\Application\UpdateTechnology;

final readonly class UpdateTechnologyCommand
{
    public function __construct(
        public string $technologyId,
        public ?string $name,
    ) {}
}
