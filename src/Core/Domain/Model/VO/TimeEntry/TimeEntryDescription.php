<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\TimeEntry;

/**
 * Optional comment/description for a time entry. Max 500 chars.
 * (Legacy DB uses VARCHAR(150) for the 'comment' column, so we cap at 150.)
 */
final readonly class TimeEntryDescription
{
    /** Max length enforced at the Request DTO layer via #[Assert\Length(max: 500)]. */
    public const int MAX_LENGTH = 500;

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
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
