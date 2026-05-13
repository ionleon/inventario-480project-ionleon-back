<?php

namespace App\ClientManagement\Application\CreateClient;

final readonly class CreateClientCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sectorId,
    ) {}
}
