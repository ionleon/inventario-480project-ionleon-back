<?php

namespace App\TimeManagement\Application\CreateTimeEntry;

use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\TimeManagement\Domain\TimeEntry;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateTimeEntryHandler
{
    public function __construct(
        private readonly TimeEntryRepositoryInterface $repository,
        private readonly ProjectUserRepositoryInterface $projectUserRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly AppUserRepositoryInterface $userRepository,
    ) {}

    public function handle(CreateTimeEntryCommand $command, ?string $currentUserId = null): TimeEntry
    {
        $projectUser = null;

        if ($command->projectUserId) {
            $projectUser = $this->projectUserRepository->findById($command->projectUserId);
        } elseif ($command->projectId && $command->userId) {
            $project = $this->projectRepository->findById($command->projectId);
            $user = $this->userRepository->findById($command->userId);

            if ($project && $user) {
                $projectUser = $this->projectUserRepository->findOneByProjectAndUser(
                    $command->projectId,
                    $command->userId
                );
            }
        }

        if (!$projectUser) {
            throw new \DomainException('Project-User relation does not exist or user is not assigned to this project.');
        }

        // Si hay un currentUserId, verificar que pertenece a él
        if ($currentUserId && $projectUser->getAppUser()->getId()->toRfc4122() !== $currentUserId) {
            throw new \LogicException('The assignment does not belong to the specified user.');
        }

        $timeEntry = new TimeEntry();
        $timeEntry->setId(Uuid::fromString($command->id));
        $timeEntry->setProjectUser($projectUser);

        try {
            $timeEntry->setDate(new \DateTime($command->date));
        } catch (\Exception) {
            throw new \InvalidArgumentException('Date format invalid. Use YYYY-MM-DD.');
        }

        $timeEntry->setHour((string) $command->hour);
        $timeEntry->setComment($command->comment);

        $this->repository->save($timeEntry);

        return $timeEntry;
    }
}
