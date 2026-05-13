<?php

namespace App\ProjectManagement\Application\CreateProject;

use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateProjectHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ClientRepositoryInterface $clientRepository,
    ) {}

    public function handle(CreateProjectCommand $command): Project
    {
        $client = $this->clientRepository->findById($command->clientId);

        if (!$client) {
            throw new \DomainException("Client with ID {$command->clientId} not found.");
        }

        $project = Project::create(
            Uuid::fromString($command->id),
            $command->name,
            $command->description,
            $client,
            $command->startDate ? new \DateTime($command->startDate) : null
        );

        $this->projectRepository->save($project);

        return $project;
    }
}
