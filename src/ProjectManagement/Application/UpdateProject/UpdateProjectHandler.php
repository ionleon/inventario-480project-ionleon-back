<?php

namespace App\ProjectManagement\Application\UpdateProject;

use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;

final class UpdateProjectHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ClientRepositoryInterface $clientRepository,
    ) {}

    public function handle(UpdateProjectCommand $command): Project
    {
        $project = $this->projectRepository->findById($command->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        $client = $this->clientRepository->findById($command->clientId);

        if (!$client) {
            throw new \DomainException("Client with ID {$command->clientId} not found.");
        }

        $project->updateDetails(
            $command->name,
            $command->description,
            $client,
            $command->startDate ? new \DateTime($command->startDate) : null,
            $command->isActive
        );

        $this->projectRepository->save($project);

        return $project;
    }
}
