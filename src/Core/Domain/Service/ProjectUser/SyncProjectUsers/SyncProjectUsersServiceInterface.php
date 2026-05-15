<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\ProjectUser\SyncProjectUsers;

use App\Core\Domain\Model\VO\Project\ProjectId;

interface SyncProjectUsersServiceInterface
{
    /**
     * @param list<array{userId: string, roleId: string, allocation: int}> $users
     */
    public function __invoke(ProjectId $projectId, array $users): void;
}
