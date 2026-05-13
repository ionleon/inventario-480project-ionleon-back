<?php

namespace App\ProjectManagement\Domain\ProjectUser;

use App\Shared\Domain\Pagination\PaginatedResult;

interface ProjectUserRepositoryInterface
{

    public function findById(string $id): ProjectUser;

    public function findOneByProjectAndUser(string $projectId, string $userId): ?ProjectUser;

    public function findByProjectPaginated(string $projectId, int $page, int $limit): PaginatedResult;

    public function save(ProjectUser $assignment, bool $flush): void;

    public function remove(ProjectUser $assignment): void;

    // Para el proceso de sync, necesitamos persistir varios pero hacer el flush al final
    public function transaction(callable $operation): void;
}
