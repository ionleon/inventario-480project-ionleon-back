<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\VO\Project;

enum DevelopmentStatus: string
{
    case PLANNED = 'PLANNED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case BLOCKED = 'BLOCKED';
    case COMPLETED = 'COMPLETED';
}
