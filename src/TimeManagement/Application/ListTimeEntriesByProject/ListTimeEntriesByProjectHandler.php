<?php

namespace App\TimeManagement\Application\ListTimeEntriesByProject;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;
use App\UserManagement\Domain\AppUserRepositoryInterface;

final class ListTimeEntriesByProjectHandler
{
    public function __construct(
        private readonly TimeEntryRepositoryInterface $repository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly AppUserRepositoryInterface $userRepository,
    ) {}

    public function handle(ListTimeEntriesByProjectQuery $query): \App\Shared\Domain\Pagination\PaginatedResult
    {
        $project = $this->projectRepository->findById($query->projectId);

        if (!$project) {
            throw new \DomainException('Project not found');
        }

        $user = null;
        if ($query->userId) {
            $user = $this->userRepository->findById($query->userId);
        }

        return $this->repository->findByProjectAndUserPaginated($project, $user, $query->page, $query->limit);
    }
}
