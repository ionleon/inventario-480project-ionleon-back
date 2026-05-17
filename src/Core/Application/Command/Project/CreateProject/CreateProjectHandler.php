<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Project\CreateProject;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectDescription;
use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Project\CreateProject\CreateProjectServiceInterface;
use DateTimeImmutable;

final readonly class CreateProjectHandler implements CommandHandler
{
    public function __construct(
        private CreateProjectServiceInterface $service,
    ) {
    }

    public function __invoke(CreateProjectCommand $command): void
    {
        $technologyIds = array_map(
            static fn(string $id) => new TechnologyId($id),
            $command->technologyIds,
        );

        ($this->service)(
            id: new ProjectId($command->id),
            name: new ProjectName($command->name),
            description: $command->description !== null ? new ProjectDescription($command->description) : null,
            clientId: new ClientId($command->clientId),
            managerId: new UserId($command->managerId),
            technologyIds: $technologyIds,
            startDate: $command->startDate !== null ? new ProjectStartDate(new DateTimeImmutable($command->startDate)) : null,
            endDate: $command->endDate !== null ? new ProjectEndDate(new DateTimeImmutable($command->endDate)) : null,
        );
    }
}
