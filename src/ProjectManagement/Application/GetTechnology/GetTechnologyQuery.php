<?php

namespace App\ProjectManagement\Application\GetTechnology;

final readonly class GetTechnologyQuery
{
    public function __construct(
        public string $technologyId,
    ) {}
}
