<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\VO\Project\DevelopmentProgress;

final class DevelopmentProgressMother
{
    public static function create(int $value = 0): DevelopmentProgress
    {
        return new DevelopmentProgress($value);
    }

    public static function zero(): DevelopmentProgress
    {
        return new DevelopmentProgress(0);
    }

    public static function complete(): DevelopmentProgress
    {
        return new DevelopmentProgress(100);
    }
}
