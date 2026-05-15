<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\AssignUserToProject;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class AssignUserToProjectCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $projectId,
        public string $userId,
        public string $roleId,
        public int $allocation,
    ) {}
}
