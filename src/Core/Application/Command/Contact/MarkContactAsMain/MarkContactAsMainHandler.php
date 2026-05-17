<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Contact\MarkContactAsMain;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Service\Contact\MarkContactAsMain\MarkContactAsMainServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class MarkContactAsMainHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private MarkContactAsMainServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(MarkContactAsMainCommand $command): void
    {
        $id = new ContactId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
