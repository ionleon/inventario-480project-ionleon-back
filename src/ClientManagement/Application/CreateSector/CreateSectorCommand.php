<?php

namespace App\ClientManagement\Application\CreateSector;

final readonly class CreateSectorCommand
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
