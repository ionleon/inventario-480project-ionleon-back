<?php

namespace App\ClientManagement\Application\UpdateClient;

final readonly class UpdateClientCommand
{
    public function __construct(
        public string $clientId,
        public ?string $name,
        public ?bool $isActive,
        public ?string $sectorId,
    ) {}
}
