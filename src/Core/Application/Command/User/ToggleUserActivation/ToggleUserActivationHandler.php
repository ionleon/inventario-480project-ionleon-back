<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\ToggleUserActivation;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\ToggleUserActivation\ToggleUserActivationServiceInterface;

final readonly class ToggleUserActivationHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private ToggleUserActivationServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(ToggleUserActivationCommand $command): void
    {
        $id = new UserId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
