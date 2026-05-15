<?php

namespace App\ClientManagement\Application\UpdateSector;

final readonly class UpdateSectorCommand
{
    public function __construct(
        public string $sectorId,
        public ?string $name,
    ) {}
}
