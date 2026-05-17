<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Project\ToggleProjectActivation;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Service\Project\ToggleProjectActivation\ToggleProjectActivationServiceInterface;

final readonly class ToggleProjectActivationHandler implements CommandHandler
{
    public function __construct(
        private ToggleProjectActivationServiceInterface $service,
    ) {
    }

    public function __invoke(ToggleProjectActivationCommand $command): void
    {
        ($this->service)(new ProjectId($command->id));
    }
}
