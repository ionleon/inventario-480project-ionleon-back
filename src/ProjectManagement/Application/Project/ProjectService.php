<?php

declare(strict_types=1);

namespace App\ProjectManagement\Application\Project;

use App\ClientManagement\Domain\ClientRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use Exception;
use Symfony\Component\Uid\Uuid;

class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private ClientRepositoryInterface  $clientRepository
    ) {}

    /**
     * @throws Exception
     */
    public function create(ProjectInputDTO $dto) : Project
    {
        $client = $this->clientRepository->findById($dto->clientId);

        if (!$client) {
            throw new Exception("Client with ID {$dto->clientId} not found.");
        }

        $project = Project::create(
            UUid::fromString($dto->id),
            $dto->name,
            $dto->description,
            $client,
            $dto->startDate ? new \DateTime($dto->startDate) : null
        );


         $this->projectRepository->save($project);

         return $project;
    }

    /**
     * @throws Exception
     */
    public function updateProject(Project $project, ProjectInputDTO $dto): void
    {
        $client = $this->clientRepository->findById($dto->clientId);

        if (!$client) {
            throw new Exception("Client with ID {$dto->clientId} not found.");
        }

        $startDate = $dto->startDate ? new \DateTime($dto->startDate) : null;

        $project->updateDetails(
            $dto->name,
            $dto->description,
            $client,
            $startDate,
            $dto->isActive
        );

        $this->projectRepository->save($project);
    }

    public function deleteProject(Project $project): void
    {
        $this->projectRepository->delete($project);
    }

    public function setProjectActivation(Project $project, bool $isActive): void
    {
        $project->setIsActive($isActive);
        $this->projectRepository->save($project);
    }
}
