<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\ListProjectUsers;

use App\Core\Domain\Model\Aggregate\ProjectUser;

final readonly class ProjectUserResponse
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $userId,
        public string $roleId,
        public int $allocation,
        public bool $isActive,
    ) {
    }

    public static function from(ProjectUser $projectUser): self
    {
        return new self(
            id: (string) $projectUser->id(),
            projectId: (string) $projectUser->projectId(),
            userId: (string) $projectUser->userId(),
            roleId: (string) $projectUser->roleId(),
            allocation: $projectUser->allocation()->value(),
            isActive: $projectUser->isActive(),
        );
    }
}
