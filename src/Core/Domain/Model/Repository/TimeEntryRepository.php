<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\TimeEntry\TimeEntryNotFoundException;
use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;

interface TimeEntryRepository
{
    public function add(TimeEntry $timeEntry): void;

    public function remove(TimeEntry $timeEntry): void;

    public function find(TimeEntryId $id): ?TimeEntry;

    /** @throws TimeEntryNotFoundException */
    public function findOneOrFail(TimeEntryId $id): TimeEntry;

    /** @return list<TimeEntry> */
    public function findByProjectUser(ProjectUserId $projectUserId): array;

    /** @return list<TimeEntry> */
    public function findByUser(UserId $userId): array;

    /** @return list<TimeEntry> */
    public function findByProject(ProjectId $projectId): array;

    /** @throws TimeEntryNotFoundException */
    public function findOwnerUserId(TimeEntryId $id): UserId;
}
