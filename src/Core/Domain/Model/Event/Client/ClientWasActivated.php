<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Client;

use App\Core\Domain\Model\VO\Client\ClientId;
use DateTimeImmutable;

final readonly class ClientWasActivated
{
    public function __construct(
        public ClientId $id,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\Client $client): self
    {
        return new self(
            id: $client->id(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
