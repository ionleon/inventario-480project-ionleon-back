<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\UpdateTimeEntry;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\TimeEntry\UpdateTimeEntry\UpdateTimeEntryServiceInterface;

final readonly class UpdateTimeEntryHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private UpdateTimeEntryServiceInterface $service,
        private TimeEntryRepository $timeEntryRepository,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(UpdateTimeEntryCommand $command): void
    {
        $id = new TimeEntryId($command->id);
        $ownerId = $this->timeEntryRepository->findOwnerUserId($id);

        $this->checkSecurity($command->securityToken, $ownerId);

        ($this->service)(
            id: $id,
            date: new TimeEntryDate($command->date),
            hours: new TimeEntryHours($command->hours),
            description: TimeEntryDescription::fromNullable($command->description),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
