<?php

namespace App\ClientManagement\Application\GetSector;

final readonly class GetSectorQuery
{
    public function __construct(
        public string $sectorId,
    ) {}
}
