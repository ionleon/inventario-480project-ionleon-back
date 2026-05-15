<?php

namespace App\ProjectManagement\Application\DeleteProject;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;

final class DeleteProjectHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(DeleteProjectCommand $command): void
    {
        $project = $this->projectRepository->findById($command->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        $this->projectRepository->delete($project);
    }
}
