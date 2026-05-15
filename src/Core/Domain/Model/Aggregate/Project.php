<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Exception\Project\InvalidProjectDateRangeException;
use App\Core\Domain\Model\Event\Project\ProjectDevelopmentWasUpdated;
use App\Core\Domain\Model\Event\Project\ProjectWasCreated;
use App\Core\Domain\Model\Event\Project\ProjectWasUpdated;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Project\DevelopmentNotes;
use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use App\Core\Domain\Model\VO\Project\DevelopmentStatus;
use App\Core\Domain\Model\VO\Project\ProjectDescription;
use App\Core\Domain\Model\VO\Project\ProjectEndDate;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Core\Domain\Model\VO\Project\ProjectStartDate;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\User\UserId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Project aggregate root.
 *
 * Many-to-many strategy: Doctrine maps a Collection<Technology> in the private
 * $technologies property (used only at persistence/hydration level). The domain
 * exposes technologyIds(): list<TechnologyId> which maps over that collection.
 * This keeps the domain interface ID-based while Doctrine handles join-table tracking.
 */
class Project extends AggregateRoot
{
    /**
     * Doctrine-managed collection for the project_technology join table.
     * Not exposed directly; use technologyIds() for domain access.
     *
     * @var Collection<int, Technology>
     */
    private Collection $technologies;

    private function __construct(
        private readonly ProjectId $id,
        private ProjectName $name,
        private ?ProjectDescription $description,
        private ClientId $clientId,
        private UserId $managerId,
        private ?ProjectStartDate $startDate,
        private ?ProjectEndDate $endDate,
        private bool $isActive,
        private DevelopmentStatus $developmentStatus,
        private ?DevelopmentNotes $developmentNotes,
        private DevelopmentProgress $developmentProgress,
    ) {
        $this->technologies = new ArrayCollection();
    }

    /**
     * @param list<Technology> $technologies
     */
    public static function create(
        ProjectId $id,
        ProjectName $name,
        ?ProjectDescription $description,
        ClientId $clientId,
        UserId $managerId,
        array $technologies,
        ?ProjectStartDate $startDate = null,
        ?ProjectEndDate $endDate = null,
    ): self {
        if ($endDate !== null && $startDate !== null && $endDate->isBefore($startDate)) {
            throw new InvalidProjectDateRangeException();
        }

        $instance = new self(
            id: $id,
            name: $name,
            description: $description,
            clientId: $clientId,
            managerId: $managerId,
            startDate: $startDate,
            endDate: $endDate,
            isActive: true,
            developmentStatus: DevelopmentStatus::PLANNED,
            developmentNotes: null,
            developmentProgress: new DevelopmentProgress(0),
        );

        foreach ($technologies as $technology) {
            $instance->technologies->add($technology);
        }

        $instance->recordEvent(ProjectWasCreated::from($instance));

        return $instance;
    }

    /**
     * @param list<Technology> $technologies
     */
    public function update(
        ProjectName $name,
        ?ProjectDescription $description,
        ClientId $clientId,
        UserId $managerId,
        array $technologies,
        ?ProjectStartDate $startDate,
        ?ProjectEndDate $endDate,
        bool $isActive,
    ): void {
        if ($endDate !== null && $startDate !== null && $endDate->isBefore($startDate)) {
            throw new InvalidProjectDateRangeException();
        }

        $this->name = $name;
        $this->description = $description;
        $this->clientId = $clientId;
        $this->managerId = $managerId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->isActive = $isActive;

        $this->technologies->clear();
        foreach ($technologies as $technology) {
            $this->technologies->add($technology);
        }

        $this->recordEvent(ProjectWasUpdated::from($this->id));
    }

    public function updateDevelopment(
        DevelopmentStatus $status,
        ?DevelopmentNotes $notes,
        DevelopmentProgress $progress,
    ): void {
        $this->developmentStatus = $status;
        $this->developmentNotes = $notes;
        $this->developmentProgress = $progress;
        $this->recordEvent(new ProjectDevelopmentWasUpdated($this->id, $status, $progress, new DateTimeImmutable()));
    }

    public function id(): ProjectId
    {
        return $this->id;
    }

    public function name(): ProjectName
    {
        return $this->name;
    }

    public function description(): ?ProjectDescription
    {
        return $this->description;
    }

    public function clientId(): ClientId
    {
        return $this->clientId;
    }

    public function managerId(): UserId
    {
        return $this->managerId;
    }

    public function startDate(): ?ProjectStartDate
    {
        return $this->startDate;
    }

    public function endDate(): ?ProjectEndDate
    {
        return $this->endDate;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function developmentStatus(): DevelopmentStatus
    {
        return $this->developmentStatus;
    }

    public function developmentNotes(): ?DevelopmentNotes
    {
        return $this->developmentNotes;
    }

    public function developmentProgress(): DevelopmentProgress
    {
        return $this->developmentProgress;
    }

    /**
     * @return list<TechnologyId>
     */
    public function technologyIds(): array
    {
        return array_values(
            $this->technologies->map(
                static fn(Technology $t) => $t->id()
            )->toArray()
        );
    }

    /**
     * @return Collection<int, Technology>
     *
     * @internal Used by Doctrine and infrastructure layer only.
     */
    public function technologies(): Collection
    {
        return $this->technologies;
    }
}
