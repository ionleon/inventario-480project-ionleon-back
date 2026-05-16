<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\TimeEntry;

use App\Core\Domain\Exception\VO\InvalidTimeEntryDateException;
use DateTimeImmutable;

final readonly class TimeEntryDate
{
    private DateTimeImmutable $value;

    public function __construct(string|DateTimeImmutable $value)
    {
        if (is_string($value)) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $value);
            if ($parsed === false) {
                throw new InvalidTimeEntryDateException($value);
            }
            $this->value = $parsed->setTime(0, 0, 0);
        } else {
            $this->value = $value->setTime(0, 0, 0);
        }
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function format(string $format): string
    {
        return $this->value->format($format);
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function equals(self $other): bool
    {
        return $this->value == $other->value;
    }
}
