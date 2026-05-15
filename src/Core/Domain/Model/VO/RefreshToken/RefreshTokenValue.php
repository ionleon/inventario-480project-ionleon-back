<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\RefreshToken;

final readonly class RefreshTokenValue
{
    private string $value;

    public function __construct(string $value)
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('RefreshToken value cannot be empty.');
        }
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
