<?php

namespace App\UserManagement\Domain;

class UserFilters
{
    public function __construct(
        public ?string $term = null,
        public ?string $role = null,
        public ?bool $isActive = null,
    ) {}

}
