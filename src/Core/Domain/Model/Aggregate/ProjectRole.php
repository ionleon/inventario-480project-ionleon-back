<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\ProjectRole\ProjectRoleWasCreated;
use App\Core\Domain\Model\Event\ProjectRole\ProjectRoleWasDeleted;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;

class ProjectRole extends AggregateRoot
{
    private function __construct(
        private ProjectRoleId $id,
        private ProjectRoleName $name,
    ) {}

    public static function create(
        ProjectRoleId $id,
        ProjectRoleName $name,
    ): self {
        $instance = new self(
            id: $id,
            name: $name,
        );

        $instance->recordEvent(ProjectRoleWasCreated::from($instance));

        return $instance;
    }

    public function id(): ProjectRoleId
    {
        return $this->id;
    }

    public function name(): ProjectRoleName
    {
        return $this->name;
    }

    public function delete(): void
    {
        $this->recordEvent(ProjectRoleWasDeleted::from($this));
    }
}
