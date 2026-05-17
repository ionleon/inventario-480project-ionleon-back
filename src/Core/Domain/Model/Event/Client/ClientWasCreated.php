<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Client;

use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;
use DateTimeImmutable;

final readonly class ClientWasCreated
{
    public function __construct(
        public ClientId $id,
        public ClientName $name,
        public SectorId $sectorId,
        public bool $isActive,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\Client $client): self
    {
        return new self(
            id: $client->id(),
            name: $client->name(),
            sectorId: $client->sectorId(),
            isActive: $client->isActive(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
