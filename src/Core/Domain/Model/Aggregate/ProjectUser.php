<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasActivated;
use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasDeactivated;
use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasRemoved;
use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasUpdated;
use App\Core\Domain\Model\Event\ProjectUser\UserWasAssignedToProject;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use DateTimeImmutable;

class ProjectUser extends AggregateRoot
{
    private function __construct(
        private ProjectUserId $id,
        private readonly ProjectId $projectId,
        private readonly UserId $userId,
        private ProjectRoleId $roleId,
        private ProjectUserAllocation $allocation,
        private bool $isActive,
    ) {
    }

    public static function assign(
        ProjectUserId $id,
        ProjectId $projectId,
        UserId $userId,
        ProjectRoleId $roleId,
        ProjectUserAllocation $allocation,
    ): self {
        $instance = new self($id, $projectId, $userId, $roleId, $allocation, true);
        $instance->recordEvent(UserWasAssignedToProject::from($instance));
        return $instance;
    }

    public function update(ProjectRoleId $roleId, ProjectUserAllocation $allocation): void
    {
        $this->roleId = $roleId;
        $this->allocation = $allocation;
        $this->recordEvent(new ProjectUserWasUpdated($this->id, new DateTimeImmutable()));
    }

    public function activate(): void
    {
        if ($this->isActive) {
            return;
        }
        $this->isActive = true;
        $this->recordEvent(new ProjectUserWasActivated($this->id, new DateTimeImmutable()));
    }

    public function deactivate(): void
    {
        if (!$this->isActive) {
            return;
        }
        $this->isActive = false;
        $this->recordEvent(new ProjectUserWasDeactivated($this->id, new DateTimeImmutable()));
    }

    public function toggleActivation(): void
    {
        if ($this->isActive) {
            $this->deactivate();
        } else {
            $this->activate();
        }
    }

    public function remove(): void
    {
        $this->recordEvent(new ProjectUserWasRemoved(
            $this->id,
            $this->projectId,
            $this->userId,
            new DateTimeImmutable(),
        ));
    }

    public function id(): ProjectUserId
    {
        return $this->id;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function roleId(): ProjectRoleId
    {
        return $this->roleId;
    }

    public function allocation(): ProjectUserAllocation
    {
        return $this->allocation;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
