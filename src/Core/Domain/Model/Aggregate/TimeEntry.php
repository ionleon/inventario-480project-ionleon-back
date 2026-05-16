<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\TimeEntry\TimeEntryWasCreated;
use App\Core\Domain\Model\Event\TimeEntry\TimeEntryWasDeleted;
use App\Core\Domain\Model\Event\TimeEntry\TimeEntryWasUpdated;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDate;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryDescription;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryHours;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use DateTimeImmutable;

class TimeEntry extends AggregateRoot
{
    private function __construct(
        private readonly TimeEntryId $id,
        private readonly ProjectUserId $projectUserId,
        private TimeEntryDate $date,
        private TimeEntryHours $hours,
        private ?TimeEntryDescription $description,
    ) {}

    public static function create(
        TimeEntryId $id,
        ProjectUserId $projectUserId,
        TimeEntryDate $date,
        TimeEntryHours $hours,
        ?TimeEntryDescription $description = null,
    ): self {
        $instance = new self($id, $projectUserId, $date, $hours, $description);
        $instance->recordEvent(new TimeEntryWasCreated(
            id: $id,
            projectUserId: $projectUserId,
            date: $date,
            hours: $hours,
            occurredAt: new DateTimeImmutable(),
        ));
        return $instance;
    }

    public function update(
        TimeEntryDate $date,
        TimeEntryHours $hours,
        ?TimeEntryDescription $description,
    ): void {
        $this->date = $date;
        $this->hours = $hours;
        $this->description = $description;
        $this->recordEvent(new TimeEntryWasUpdated($this->id, new DateTimeImmutable()));
    }

    public function delete(): void
    {
        $this->recordEvent(new TimeEntryWasDeleted($this->id, new DateTimeImmutable()));
    }

    public function id(): TimeEntryId
    {
        return $this->id;
    }

    public function projectUserId(): ProjectUserId
    {
        return $this->projectUserId;
    }

    public function date(): TimeEntryDate
    {
        return $this->date;
    }

    public function hours(): TimeEntryHours
    {
        return $this->hours;
    }

    public function description(): ?TimeEntryDescription
    {
        return $this->description;
    }
}
