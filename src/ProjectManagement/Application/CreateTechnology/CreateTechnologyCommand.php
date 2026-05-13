<?php

namespace App\ProjectManagement\Application\CreateTechnology;

final readonly class CreateTechnologyCommand
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
