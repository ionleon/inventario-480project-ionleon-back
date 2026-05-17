<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\SyncProjectUsers;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Service\ProjectUser\SyncProjectUsers\SyncProjectUsersServiceInterface;

final readonly class SyncProjectUsersHandler implements CommandHandler
{
    public function __construct(
        private SyncProjectUsersServiceInterface $service,
    ) {
    }

    public function __invoke(SyncProjectUsersCommand $command): void
    {
        ($this->service)(
            projectId: new ProjectId($command->projectId),
            users: $command->users,
        );
    }
}
