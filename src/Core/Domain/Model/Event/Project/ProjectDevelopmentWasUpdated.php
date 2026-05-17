<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Project;

use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use App\Core\Domain\Model\VO\Project\DevelopmentStatus;
use App\Core\Domain\Model\VO\Project\ProjectId;
use DateTimeImmutable;

final readonly class ProjectDevelopmentWasUpdated
{
    public function __construct(
        public ProjectId $id,
        public DevelopmentStatus $status,
        public DevelopmentProgress $progress,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
