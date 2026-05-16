<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\TimeEntry;

use App\Core\Domain\Exception\VO\InvalidTimeEntryHoursException;

/**
 * Hours worked in a time entry. Decimal > 0 and <= 24.
 * Stored as string to preserve NUMERIC(7,2) precision.
 */
final readonly class TimeEntryHours
{
    private string $value;

    public function __construct(string|float|int $value)
    {
        $float = (float) $value;

        if ($float <= 0 || $float > 24) {
            throw new InvalidTimeEntryHoursException((string) $value);
        }

        // Store with 2 decimal places to match NUMERIC(7,2)
        $this->value = number_format($float, 2, '.', '');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toFloat(): float
    {
        return (float) $this->value;
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
