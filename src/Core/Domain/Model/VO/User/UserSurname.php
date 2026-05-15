<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\User;

use App\Core\Domain\Exception\VO\InvalidUserSurnameException;

final readonly class UserSurname
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (mb_strlen($value) < 1 || mb_strlen($value) > 100) {
            throw new InvalidUserSurnameException($value);
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
