<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\UpdateTimeEntry;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Service\TimeEntry\UpdateTimeEntry\UpdateTimeEntryServiceInterface;

final readonly class UpdateTimeEntryHandler implements CommandHandler
{
    public function __construct(
        private UpdateTimeEntryServiceInterface $service,
    ) {}

    public function __invoke(UpdateTimeEntryCommand $command): void
    {
        ($this->service)(
            id: new TimeEntryId($command->id),
            date: new TimeEntryDate($command->date),
            hours: new TimeEntryHours($command->hours),
            description: TimeEntryDescription::fromNullable($command->description),
        );
    }
}
