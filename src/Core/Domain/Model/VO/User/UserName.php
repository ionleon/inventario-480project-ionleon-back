<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\User;

use App\Core\Domain\Exception\VO\InvalidUserNameException;

final readonly class UserName
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (mb_strlen($value) < 3 || mb_strlen($value) > 100) {
            throw new InvalidUserNameException($value);
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
