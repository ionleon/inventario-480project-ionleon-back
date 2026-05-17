<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\RefreshToken;

use App\Core\Domain\Model\Aggregate\RefreshToken;
use DateTimeImmutable;
use DateTimeInterface;

final readonly class RefreshTokenWasIssued
{
    public function __construct(
        public ?int $id,
        public string $refreshToken,
        public string $username,
        public ?DateTimeInterface $valid,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(RefreshToken $token): self
    {
        return new self(
            id: $token->getId(),
            refreshToken: (string) $token->getRefreshToken(),
            username: (string) $token->getUsername(),
            valid: $token->getValid(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
