<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\VO\Project\ProjectId;

final class ProjectIdMother
{
    public static function create(?string $value = null): ProjectId
    {
        return new ProjectId($value ?? ProjectId::generate()->__toString());
    }
}
