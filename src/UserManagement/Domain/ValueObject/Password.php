<?php

namespace App\UserManagement\Domain\ValueObject;

final readonly class Password
{
    public function __construct(public string $value)
    {
        if (strlen($value) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long.');
        }
    }
}
