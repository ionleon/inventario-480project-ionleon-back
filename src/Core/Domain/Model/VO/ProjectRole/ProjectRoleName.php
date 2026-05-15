<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\ProjectRole;

final readonly class ProjectRoleName
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) < 2 || mb_strlen($value) > 80) {
            throw new \InvalidArgumentException(
                sprintf('ProjectRoleName must be between 2 and 80 characters, got "%s".', $value)
            );
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
