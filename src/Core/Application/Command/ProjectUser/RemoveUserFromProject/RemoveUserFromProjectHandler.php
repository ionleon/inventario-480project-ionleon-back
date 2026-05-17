<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\RemoveUserFromProject;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Service\ProjectUser\RemoveUserFromProject\RemoveUserFromProjectServiceInterface;

final readonly class RemoveUserFromProjectHandler implements CommandHandler
{
    public function __construct(
        private RemoveUserFromProjectServiceInterface $service,
    ) {
    }

    public function __invoke(RemoveUserFromProjectCommand $command): void
    {
        ($this->service)(new ProjectUserId($command->id));
    }
}
