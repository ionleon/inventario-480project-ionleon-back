<?php

namespace App\ProjectManagement\Domain\ProjectUser;

use App\Shared\Domain\Pagination\PaginatedResult;

interface ProjectUserRepositoryInterface
{
    public function findOneByProjectAndUser(int $projectId, int $userId): ?ProjectUser;

    public function findByProjectPaginated(int $projectId, int $page, int $limit): PaginatedResult;

    public function save(ProjectUser $assignment): void;

    public function remove(ProjectUser $assignment): void;

    // Para el proceso de sync, necesitamos persistir varios pero hacer el flush al final
    public function transaction(callable $operation): void;
}
