<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\SyncProjectUsers;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class SyncProjectUsersCommand implements Command
{
    /**
     * @param list<array{userId: string, roleId: string, allocation: int}> $users
     */
    public function __construct(
        public SecurityToken $securityToken,
        public string $projectId,
        public array $users,
    ) {}
}
