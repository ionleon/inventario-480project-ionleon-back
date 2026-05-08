<?php

namespace App\ProjectManagement\Domain\Project;

readonly class ProjectFilters
{
     public function __construct(
         public ?string $term = null,
         public ?int $clientId = null,
         public ?bool $isActive = null,
     )
     {}
}
