<?php

namespace App\ProjectManagement\Application\DeleteTechnology;

final readonly class DeleteTechnologyCommand
{
    public function __construct(
        public string $technologyId,
    ) {}
}
