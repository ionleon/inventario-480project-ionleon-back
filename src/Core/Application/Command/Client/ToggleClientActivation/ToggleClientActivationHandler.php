<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Client\ToggleClientActivation;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Service\Client\ToggleClientActivation\ToggleClientActivationServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class ToggleClientActivationHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private ToggleClientActivationServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(ToggleClientActivationCommand $command): void
    {
        $id = new ClientId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
