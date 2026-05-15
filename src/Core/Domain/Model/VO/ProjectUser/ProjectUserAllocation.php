<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\ProjectUser;

use App\Core\Domain\Exception\VO\InvalidProjectUserAllocationException;

/**
 * Allocation percentage 0–100.
 */
final readonly class ProjectUserAllocation
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new InvalidProjectUserAllocationException($value);
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
