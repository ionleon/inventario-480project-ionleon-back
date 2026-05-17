<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\GetClient;

use App\Core\Domain\Model\Aggregate\Client;

final readonly class GetClientResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sectorId,
        public bool $isActive,
    ) {
    }

    public static function from(Client $client): self
    {
        return new self(
            id: (string) $client->id(),
            name: (string) $client->name(),
            sectorId: (string) $client->sectorId(),
            isActive: $client->isActive(),
        );
    }
}
