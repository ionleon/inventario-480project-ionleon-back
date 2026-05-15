<?php

namespace App\TimeManagement\Domain;

use App\ProjectManagement\Domain\Project\Project;
use App\Shared\Domain\Pagination\PaginatedResult;
use App\UserManagement\Domain\AppUser;

interface TimeEntryRepositoryInterface
{
    public function findById(string $id): ?TimeEntry;

    public function findByProjectAndUserPaginated(Project $project, ?AppUser $user, int $page, int $limit): PaginatedResult;

    public function findByUserPaginated(AppUser $user, int $page, int $limit): PaginatedResult;

    public function getTotalHoursByUser(AppUser $user): float;

    public function save(TimeEntry $timeEntry): void;

    public function delete(TimeEntry $timeEntry): void;
}
