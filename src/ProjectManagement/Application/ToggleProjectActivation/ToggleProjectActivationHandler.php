<?php

namespace App\ProjectManagement\Application\ToggleProjectActivation;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;

final class ToggleProjectActivationHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(ToggleProjectActivationCommand $command): void
    {
        $project = $this->projectRepository->findById($command->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        $project->toggleActivation();

        $this->projectRepository->save($project);
    }
}
