<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Project\DeleteProject;

use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class DeleteProjectService implements DeleteProjectServiceInterface
{
    public function __construct(private ProjectRepository $projectRepository) {}

    public function __invoke(ProjectId $id): void
    {
        $project = $this->projectRepository->findOneOrFail($id);
        $this->projectRepository->remove($project);
    }
}
