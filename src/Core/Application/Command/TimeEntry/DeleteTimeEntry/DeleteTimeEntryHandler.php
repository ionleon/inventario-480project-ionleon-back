<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\DeleteTimeEntry;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Service\TimeEntry\DeleteTimeEntry\DeleteTimeEntryServiceInterface;

final readonly class DeleteTimeEntryHandler implements CommandHandler
{
    public function __construct(
        private DeleteTimeEntryServiceInterface $service,
    ) {}

    public function __invoke(DeleteTimeEntryCommand $command): void
    {
        ($this->service)(new TimeEntryId($command->id));
    }
}
