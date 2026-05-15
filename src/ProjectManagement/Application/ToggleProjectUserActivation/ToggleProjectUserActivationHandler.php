<?php

namespace App\ProjectManagement\Application\ToggleProjectUserActivation;

use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;

final class ToggleProjectUserActivationHandler
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
    ) {}

    public function handle(ToggleProjectUserActivationCommand $command): ProjectUser
    {
        $assignment = $this->projectUserRepository->findOneByProjectAndUser(
            $command->projectId,
            $command->userId
        );

        if (!$assignment) {
            throw new \DomainException('Assignment not found');
        }

        $assignment->toggleActivation();

        $this->projectUserRepository->save($assignment, true);

        return $assignment;
    }
}
