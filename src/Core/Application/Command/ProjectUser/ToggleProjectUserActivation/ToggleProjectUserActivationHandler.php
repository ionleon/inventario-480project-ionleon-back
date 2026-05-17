<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectUser\ToggleProjectUserActivation;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Service\ProjectUser\ToggleProjectUserActivation\ToggleProjectUserActivationServiceInterface;

final readonly class ToggleProjectUserActivationHandler implements CommandHandler
{
    public function __construct(
        private ToggleProjectUserActivationServiceInterface $service,
    ) {
    }

    public function __invoke(ToggleProjectUserActivationCommand $command): void
    {
        ($this->service)(new ProjectUserId($command->id));
    }
}
