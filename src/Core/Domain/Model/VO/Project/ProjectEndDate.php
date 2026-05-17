<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Project;

use DateTimeImmutable;

final readonly class ProjectEndDate
{
    public function __construct(private DateTimeImmutable $value)
    {
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function isBefore(ProjectStartDate $start): bool
    {
        return $this->value < $start->value();
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d');
    }
}
