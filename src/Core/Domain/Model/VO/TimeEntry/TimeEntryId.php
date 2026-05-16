<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\TimeEntry;

use App\Core\Domain\Exception\VO\InvalidTimeEntryIdException;
use Symfony\Component\Uid\Uuid;

final readonly class TimeEntryId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidTimeEntryIdException($value);
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
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
