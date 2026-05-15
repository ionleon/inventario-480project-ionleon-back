<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\RefreshToken;

use DateTimeImmutable;

final readonly class RefreshTokenExpiresAt
{
    public function __construct(private DateTimeImmutable $value) {}

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function isInFuture(): bool
    {
        return $this->value > new DateTimeImmutable();
    }
}
