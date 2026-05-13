<?php

namespace App\TimeManagement\Infrastructure\TimeEntry\Response;

use App\TimeManagement\Domain\TimeEntry;

final readonly class TimeEntryResponse
{
    public string $id;
    public string $date;
    public string $hour;
    public ?string $comment;
    public array $projectUser;
    public ?array $project;

    private function __construct(TimeEntry $timeEntry)
    {
        $this->id = $timeEntry->getId()->toRfc4122();
        $this->date = $timeEntry->getDate()->format('Y-m-d');
        $this->hour = $timeEntry->getHour();
        $this->comment = $timeEntry->getComment();

        $projectUser = $timeEntry->getProjectUser();
        $this->projectUser = [
            'id' => $projectUser->getId()->toRfc4122(),
            'user' => [
                'id' => $projectUser->getAppUser()->getId()->toRfc4122(),
                'name' => $projectUser->getAppUser()->getName(),
                'surname' => $projectUser->getAppUser()->getSurname(),
            ],
        ];

        $project = $timeEntry->getProjectForDash();
        $this->project = $project ? [
            'id' => $project->getId()->toRfc4122(),
            'name' => $project->getName(),
        ] : null;
    }

    public static function fromEntity(TimeEntry $timeEntry): self
    {
        return new self($timeEntry);
    }
}
