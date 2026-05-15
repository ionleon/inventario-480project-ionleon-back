<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Project\DeleteProject;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Service\Project\DeleteProject\DeleteProjectServiceInterface;

final readonly class DeleteProjectHandler implements CommandHandler
{
    public function __construct(
        private DeleteProjectServiceInterface $service,
    ) {}

    public function __invoke(DeleteProjectCommand $command): void
    {
        ($this->service)(new ProjectId($command->id));
    }
}
