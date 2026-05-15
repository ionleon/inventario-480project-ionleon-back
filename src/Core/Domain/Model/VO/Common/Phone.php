<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Common;

use App\Core\Domain\Exception\VO\InvalidPhoneException;

final readonly class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > 30) {
            throw new InvalidPhoneException($value);
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
