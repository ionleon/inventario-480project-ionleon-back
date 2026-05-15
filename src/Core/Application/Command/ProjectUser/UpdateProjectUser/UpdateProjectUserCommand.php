<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\UpdateProjectUser;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class UpdateProjectUserCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $roleId,
        public int $allocation,
    ) {}
}
