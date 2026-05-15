<?php

namespace App\ClientManagement\Application\DeleteSector;

final readonly class DeleteSectorCommand
{
    public function __construct(
        public string $sectorId,
    ) {}
}
