<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\UpdateProjectUser;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Service\ProjectUser\UpdateProjectUser\UpdateProjectUserServiceInterface;

final readonly class UpdateProjectUserHandler implements CommandHandler
{
    public function __construct(
        private UpdateProjectUserServiceInterface $service,
    ) {}

    public function __invoke(UpdateProjectUserCommand $command): void
    {
        ($this->service)(
            id: new ProjectUserId($command->id),
            roleId: new ProjectRoleId($command->roleId),
            allocation: new ProjectUserAllocation($command->allocation),
        );
    }
}
