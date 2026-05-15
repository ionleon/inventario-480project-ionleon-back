<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Project;

use App\Core\Domain\Exception\VO\InvalidProjectNameException;

final readonly class ProjectName
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (mb_strlen($trimmed) < 2 || mb_strlen($trimmed) > 150) {
            throw new InvalidProjectNameException($trimmed);
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
