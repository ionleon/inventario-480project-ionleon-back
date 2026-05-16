<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\TimeEntry\CreateTimeEntry;

use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;

interface CreateTimeEntryServiceInterface
{
    public function __invoke(
        TimeEntryId $id,
        ProjectId $projectId,
        UserId $userId,
        TimeEntryDate $date,
        TimeEntryHours $hours,
        ?TimeEntryDescription $description,
    ): TimeEntry;
}
