<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\TimeEntry\CreateTimeEntry;

use App\Core\Domain\Exception\TimeEntry\UserNotAssignedToProjectException;
use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;

final readonly class CreateTimeEntryService implements CreateTimeEntryServiceInterface
{
    public function __construct(
        private TimeEntryRepository $timeEntryRepository,
        private ProjectRepository $projectRepository,
        private UserRepository $userRepository,
        private ProjectUserRepository $projectUserRepository,
    ) {
    }

    public function __invoke(
        TimeEntryId $id,
        ProjectId $projectId,
        UserId $userId,
        TimeEntryDate $date,
        TimeEntryHours $hours,
        ?TimeEntryDescription $description,
    ): TimeEntry {
        $this->projectRepository->findOneOrFail($projectId);
        $this->userRepository->findOneOrFail($userId);

        $projectUser = $this->projectUserRepository->findOneByProjectAndUser($projectId, $userId);

        if ($projectUser === null || !$projectUser->isActive()) {
            throw new UserNotAssignedToProjectException();
        }

        $timeEntry = TimeEntry::create(
            id: $id,
            projectUserId: $projectUser->id(),
            date: $date,
            hours: $hours,
            description: $description,
        );

        $this->timeEntryRepository->add($timeEntry);

        return $timeEntry;
    }
}
