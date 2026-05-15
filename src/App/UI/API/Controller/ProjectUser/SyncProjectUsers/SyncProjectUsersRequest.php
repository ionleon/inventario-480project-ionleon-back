<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\SyncProjectUsers;

final readonly class SyncProjectUsersRequest
{
    /**
     * @param list<array{userId: string, roleId: string, allocation: int}> $users
     */
    public function __construct(
        public array $users = [],
    ) {}
}
