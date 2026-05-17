<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\GetProject;

use App\Core\Domain\Model\Aggregate\Project;

final readonly class GetProjectResponse
{
    /**
     * @param list<string> $technologyIds
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public string $clientId,
        public ?string $managerId,
        public array $technologyIds,
        public ?string $startDate,
        public ?string $endDate,
        public bool $isActive,
        public ?string $developmentStatus,
        public ?string $developmentNotes,
        public ?int $developmentProgress,
    ) {
    }

    public static function from(Project $project): self
    {
        return new self(
            id: (string) $project->id(),
            name: (string) $project->name(),
            description: $project->description() !== null ? (string) $project->description() : null,
            clientId: (string) $project->clientId(),
            managerId: $project->managerId() !== null ? (string) $project->managerId() : null,
            technologyIds: array_map(static fn($tid) => (string) $tid, $project->technologyIds()),
            startDate: $project->startDate() !== null ? (string) $project->startDate() : null,
            endDate: $project->endDate() !== null ? (string) $project->endDate() : null,
            isActive: $project->isActive(),
            developmentStatus: $project->developmentStatus()?->value,
            developmentNotes: $project->developmentNotes() !== null ? (string) $project->developmentNotes() : null,
            developmentProgress: $project->developmentProgress()?->value(),
        );
    }
}
