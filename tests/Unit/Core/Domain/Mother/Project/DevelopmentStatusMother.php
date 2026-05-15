<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Project;

use App\Core\Domain\Model\VO\Project\DevelopmentStatus;

final class DevelopmentStatusMother
{
    public static function planned(): DevelopmentStatus
    {
        return DevelopmentStatus::PLANNED;
    }

    public static function inProgress(): DevelopmentStatus
    {
        return DevelopmentStatus::IN_PROGRESS;
    }

    public static function create(): DevelopmentStatus
    {
        return DevelopmentStatus::PLANNED;
    }
}
