<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\RemoveUserFromProject;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class RemoveUserFromProjectCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
    ) {
    }
}
