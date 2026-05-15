<?php

namespace App\ProjectManagement\Application\RemoveUserFromProject;

use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;

final class RemoveUserFromProjectHandler
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
    ) {}

    public function handle(RemoveUserFromProjectCommand $command): void
    {
        $assignment = $this->projectUserRepository->findOneByProjectAndUser(
            $command->projectId,
            $command->userId
        );

        if (!$assignment) {
            throw new \DomainException('Assignment not found');
        }

        $this->projectUserRepository->remove($assignment);
    }
}
