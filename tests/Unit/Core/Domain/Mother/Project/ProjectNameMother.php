<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\VO\Project\ProjectName;

final class ProjectNameMother
{
    public static function create(?string $value = null): ProjectName
    {
        return new ProjectName($value ?? 'Test Project ' . uniqid());
    }
}
