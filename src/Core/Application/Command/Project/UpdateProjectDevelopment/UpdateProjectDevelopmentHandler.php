<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Project\UpdateProjectDevelopment;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Project\DevelopmentNotes;
use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use App\Core\Domain\Model\VO\Project\DevelopmentStatus;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Service\Project\UpdateProjectDevelopment\UpdateProjectDevelopmentServiceInterface;

final readonly class UpdateProjectDevelopmentHandler implements CommandHandler
{
    public function __construct(
        private UpdateProjectDevelopmentServiceInterface $service,
    ) {
    }

    public function __invoke(UpdateProjectDevelopmentCommand $command): void
    {
        ($this->service)(
            id: new ProjectId($command->id),
            status: DevelopmentStatus::from($command->status),
            notes: $command->notes !== null ? new DevelopmentNotes($command->notes) : null,
            progress: new DevelopmentProgress($command->progress),
        );
    }
}
