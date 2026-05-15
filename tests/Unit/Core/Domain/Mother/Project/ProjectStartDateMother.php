<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use DateTimeImmutable;

final class ProjectStartDateMother
{
    public static function create(?DateTimeImmutable $value = null): ProjectStartDate
    {
        return new ProjectStartDate($value ?? new DateTimeImmutable('2026-01-01'));
    }
}
