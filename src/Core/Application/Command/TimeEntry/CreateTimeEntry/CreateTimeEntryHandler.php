<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\CreateTimeEntry;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\TimeEntry\CreateTimeEntry\CreateTimeEntryServiceInterface;

final readonly class CreateTimeEntryHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private CreateTimeEntryServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(CreateTimeEntryCommand $command): void
    {
        $ownerId = new UserId($command->userId);

        $this->checkSecurity($command->securityToken, $ownerId);

        ($this->service)(
            id: new TimeEntryId($command->id),
            projectId: new ProjectId($command->projectId),
            userId: $ownerId,
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
