<?php

namespace App\ProjectManagement\Application\SyncProjectUsers;

final readonly class SyncProjectUsersCommand
{
    /**
     * @param string $projectId
     * @param array<int, array{user_id: string, role_id: string, is_active?: bool}> $users
     */
    public function __construct(
        public string $projectId,
        public array $users,
    ) {}
}
