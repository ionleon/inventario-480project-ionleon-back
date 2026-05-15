<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\AssignUserToProject;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\ProjectUser\AssignUserToProject\AssignUserToProjectServiceInterface;

final readonly class AssignUserToProjectHandler implements CommandHandler
{
    public function __construct(
        private AssignUserToProjectServiceInterface $service,
    ) {}

    public function __invoke(AssignUserToProjectCommand $command): void
    {
        ($this->service)(
            id: new ProjectUserId($command->id),
            projectId: new ProjectId($command->projectId),
            userId: new UserId($command->userId),
            roleId: new ProjectRoleId($command->roleId),
            allocation: new ProjectUserAllocation($command->allocation),
        );
    }
}
