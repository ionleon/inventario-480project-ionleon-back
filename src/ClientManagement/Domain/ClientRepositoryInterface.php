<?php

namespace App\ClientManagement\Domain;

interface ClientRepositoryInterface
{
    #Need to pass ClientFilter item through parameter
    public function findWithSectorsByFilters(?string $term, ?bool $isActive = true ): array;
}
