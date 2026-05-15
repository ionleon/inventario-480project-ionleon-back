<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\UpdateProjectDevelopment;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Project\DevelopmentNotes;
use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use App\Core\Domain\Model\VO\Project\DevelopmentStatus;
use App\Core\Domain\Model\VO\Project\ProjectId;

interface UpdateProjectDevelopmentServiceInterface
{
    /**
     * @throws ProjectNotFoundException
     */
    public function __invoke(
        ProjectId $id,
        DevelopmentStatus $status,
        ?DevelopmentNotes $notes,
        DevelopmentProgress $progress,
    ): Project;
}
