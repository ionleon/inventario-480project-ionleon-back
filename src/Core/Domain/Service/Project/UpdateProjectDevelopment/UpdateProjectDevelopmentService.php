<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\UpdateProjectDevelopment;

use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\VO\Project\DevelopmentNotes;
use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use App\Core\Domain\Model\VO\Project\DevelopmentStatus;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class UpdateProjectDevelopmentService implements UpdateProjectDevelopmentServiceInterface
{
    public function __construct(private ProjectRepository $projectRepository) {}

    public function __invoke(
        ProjectId $id,
        DevelopmentStatus $status,
        ?DevelopmentNotes $notes,
        DevelopmentProgress $progress,
    ): Project {
        $project = $this->projectRepository->findOneOrFail($id);
        $project->updateDevelopment($status, $notes, $progress);
        return $project;
    }
}
