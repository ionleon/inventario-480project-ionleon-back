<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Common;

use App\Core\Domain\Exception\VO\InvalidEmailException;

final readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $value = mb_strtolower(trim($value));
        if (mb_strlen($value) > 150 || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmailException($value);
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
