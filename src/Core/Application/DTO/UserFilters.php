<?php

declare(strict_types=1);

namespace App\Core\Application\DTO;

class UserFilters
{
    public function __construct(
        public ?string $term = null,
        public ?string $role = null,
        public ?bool $isActive = null,
    ) {
    }
}
