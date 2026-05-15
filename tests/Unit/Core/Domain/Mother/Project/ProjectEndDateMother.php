<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use DateTimeImmutable;

final class ProjectEndDateMother
{
    public static function create(?DateTimeImmutable $value = null): ProjectEndDate
    {
        return new ProjectEndDate($value ?? new DateTimeImmutable('2026-12-31'));
    }
}
