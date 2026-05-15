<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Exception\Project\InvalidProjectDateRangeException;
use App\Core\Domain\Model\Event\Project\ProjectDevelopmentWasUpdated;
use App\Core\Domain\Model\Event\Project\ProjectWasCreated;
use App\Core\Domain\Model\Event\Project\ProjectWasUpdated;
use App\Core\Domain\Model\VO\Project\DevelopmentNotes;
use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use App\Core\Domain\Model\VO\Project\DevelopmentStatus;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Project\DevelopmentProgressMother;
use App\Tests\Unit\Core\Domain\Mother\Project\DevelopmentStatusMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectEndDateMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectNameMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectStartDateMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function test_GivenValidData_WhenCreate_ThenProjectIsCreated(): void
    {
        $project = ProjectMother::create();

        $this->assertNotNull($project->id());
        $this->assertTrue($project->isActive());
        $this->assertSame(DevelopmentStatus::PLANNED, $project->developmentStatus());
        $this->assertNull($project->developmentNotes());
        $this->assertSame(0, $project->developmentProgress()->value());
        $this->assertEmpty($project->technologyIds());
    }

    public function test_GivenValidData_WhenCreate_ThenProjectWasCreatedEventFired(): void
    {
        $project = ProjectMother::create();
        $events = $project->pullEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ProjectWasCreated::class, $events[0]);
    }

    public function test_GivenEndDateBeforeStartDate_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectDateRangeException::class);

        ProjectMother::create(
            startDate: ProjectStartDateMother::create(new DateTimeImmutable('2026-12-31')),
            endDate: ProjectEndDateMother::create(new DateTimeImmutable('2026-01-01')),
        );
    }

    public function test_GivenValidDates_WhenCreate_ThenProjectIsCreated(): void
    {
        $project = ProjectMother::create(
            startDate: ProjectStartDateMother::create(new DateTimeImmutable('2026-01-01')),
            endDate: ProjectEndDateMother::create(new DateTimeImmutable('2026-12-31')),
        );

        $this->assertNotNull($project->startDate());
        $this->assertNotNull($project->endDate());
    }

    public function test_GivenUpdatedData_WhenUpdate_ThenProjectIsUpdated(): void
    {
        $project = ProjectMother::create();
        $project->pullEvents(); // clear create event

        $newName = ProjectNameMother::create('Updated Name');
        $newClientId = ClientIdMother::create();
        $newManagerId = UserIdMother::create();

        $project->update(
            name: $newName,
            description: null,
            clientId: $newClientId,
            managerId: $newManagerId,
            technologies: [],
            startDate: null,
            endDate: null,
            isActive: false,
        );

        $this->assertSame((string) $newName, (string) $project->name());
        $this->assertFalse($project->isActive());

        $events = $project->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ProjectWasUpdated::class, $events[0]);
    }

    public function test_GivenEndDateBeforeStartDate_WhenUpdate_ThenThrowsException(): void
    {
        $project = ProjectMother::create();

        $this->expectException(InvalidProjectDateRangeException::class);

        $project->update(
            name: ProjectNameMother::create(),
            description: null,
            clientId: ClientIdMother::create(),
            managerId: UserIdMother::create(),
            technologies: [],
            startDate: ProjectStartDateMother::create(new DateTimeImmutable('2026-12-31')),
            endDate: ProjectEndDateMother::create(new DateTimeImmutable('2026-01-01')),
            isActive: true,
        );
    }

    public function test_GivenDevelopmentData_WhenUpdateDevelopment_ThenDevelopmentIsUpdated(): void
    {
        $project = ProjectMother::create();
        $project->pullEvents();

        $project->updateDevelopment(
            status: DevelopmentStatus::IN_PROGRESS,
            notes: new DevelopmentNotes('Working on it'),
            progress: new DevelopmentProgress(50),
        );

        $this->assertSame(DevelopmentStatus::IN_PROGRESS, $project->developmentStatus());
        $this->assertSame('Working on it', (string) $project->developmentNotes());
        $this->assertSame(50, $project->developmentProgress()->value());

        $events = $project->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ProjectDevelopmentWasUpdated::class, $events[0]);
    }
}
