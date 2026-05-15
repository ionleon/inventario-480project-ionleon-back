<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Project;

final readonly class DevelopmentProgress
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException(
                sprintf('Development progress must be between 0 and 100, got %d.', $value)
            );
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
