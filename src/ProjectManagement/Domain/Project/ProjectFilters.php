<?php

namespace App\ProjectManagement\Domain\Project;

readonly class ProjectFilters
{
     public function __construct(
         public ?string $term = null,
         public ?string $clientId = null,
         public ?bool $isActive = null,
     )
     {}
}
