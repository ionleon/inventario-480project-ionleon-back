<?php

namespace App\ProjectManagement\Application\ListDevelopments;

use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;

final class ListDevelopmentsHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly DevelopmentRepositoryInterface $developmentRepository,
    ) {}

    public function handle(ListDevelopmentsQuery $query): array
    {
        $project = $this->projectRepository->findById($query->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        return $this->developmentRepository->findByProject($project);
    }
}
