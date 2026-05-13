<?php

namespace App\ProjectManagement\Application\GetProject;

use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;

final class GetProjectHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(GetProjectQuery $query): Project
    {
        $project = $this->projectRepository->findById($query->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        return $project;
    }
}
