<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Link;

final readonly class LinkLabel
{
    private const int MIN_LENGTH = 1;
    private const int MAX_LENGTH = 100;

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (strlen($trimmed) < self::MIN_LENGTH || strlen($trimmed) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('LinkLabel must be between %d and %d characters.', self::MIN_LENGTH, self::MAX_LENGTH),
            );
        }
        $this->value = $trimmed;
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
