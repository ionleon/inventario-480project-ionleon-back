<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\RefreshToken;

use App\Core\Domain\Model\Aggregate\RefreshToken;
use DateTimeImmutable;

final readonly class RefreshTokenWasRevoked
{
    public function __construct(
        public ?int $id,
        public string $username,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(RefreshToken $token): self
    {
        return new self(
            id: $token->getId(),
            username: (string) $token->getUsername(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
