<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Sector;

use App\Core\Domain\Exception\VO\InvalidSectorNameException;

final readonly class SectorName
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) < 2 || mb_strlen($value) > 100) {
            throw new InvalidSectorNameException($value);
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
