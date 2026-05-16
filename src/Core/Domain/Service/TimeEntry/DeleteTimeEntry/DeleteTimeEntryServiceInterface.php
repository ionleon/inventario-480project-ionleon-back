<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\TimeEntry\DeleteTimeEntry;

use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;

interface DeleteTimeEntryServiceInterface
{
    public function __invoke(TimeEntryId $id): void;
}
