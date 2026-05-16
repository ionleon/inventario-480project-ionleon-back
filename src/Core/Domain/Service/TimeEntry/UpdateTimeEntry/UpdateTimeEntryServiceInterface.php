<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\TimeEntry\UpdateTimeEntry;

use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;

interface UpdateTimeEntryServiceInterface
{
    public function __invoke(
        TimeEntryId $id,
        TimeEntryDate $date,
        TimeEntryHours $hours,
        ?TimeEntryDescription $description,
    ): TimeEntry;
}
