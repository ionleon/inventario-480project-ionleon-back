<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Common;

final readonly class Password
{
    public function __construct(private string $hashedValue) {}

    public function __toString(): string
    {
        return $this->hashedValue;
    }

    public function equalsHash(string $other): bool
    {
        return password_verify($other, $this->hashedValue);
    }
}
