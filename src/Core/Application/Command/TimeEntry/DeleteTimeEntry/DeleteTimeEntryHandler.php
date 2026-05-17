<?php

declare(strict_types=1);

namespace App\Core\Application\Command\TimeEntry\DeleteTimeEntry;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\TimeEntry\DeleteTimeEntry\DeleteTimeEntryServiceInterface;

final readonly class DeleteTimeEntryHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private DeleteTimeEntryServiceInterface $service,
        private TimeEntryRepository $timeEntryRepository,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(DeleteTimeEntryCommand $command): void
    {
        $id = new TimeEntryId($command->id);
        $ownerId = $this->timeEntryRepository->findOwnerUserId($id);

        $this->checkSecurity($command->securityToken, $ownerId);

        ($this->service)($id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
