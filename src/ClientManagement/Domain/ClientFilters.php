<?php

namespace App\ClientManagement\Domain;

class ClientFilters
{
    public function __construct(
        public ?string $term = null,
        public ?bool $isActive = null,
    )
    {}
}
