<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Project;

use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\User\UserId;
use DateTimeImmutable;

final readonly class ProjectWasCreated
{
    public function __construct(
        public ProjectId $id,
        public ProjectName $name,
        public ClientId $clientId,
        public UserId $managerId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public static function from(Project $project): self
    {
        return new self(
            id: $project->id(),
            name: $project->name(),
            clientId: $project->clientId(),
            managerId: $project->managerId(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
