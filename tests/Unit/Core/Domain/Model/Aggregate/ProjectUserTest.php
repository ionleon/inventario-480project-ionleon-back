<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasActivated;
use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasDeactivated;
use App\Core\Domain\Model\Event\ProjectUser\ProjectUserWasUpdated;
use App\Core\Domain\Model\Event\ProjectUser\UserWasAssignedToProject;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserAllocationMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;
use PHPUnit\Framework\TestCase;

final class ProjectUserTest extends TestCase
{
    public function test_GivenValidData_WhenAssign_ThenProjectUserCreatedWithEvent(): void
    {
        $projectUser = ProjectUserMother::create();

        $this->assertTrue($projectUser->isActive());

        $events = $projectUser->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasAssignedToProject::class, $events[0]);
    }

    public function test_GivenActiveProjectUser_WhenUpdate_ThenUpdatedEventRecorded(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents(); // clear assign event

        $newRoleId = ProjectRoleIdMother::create();
        $newAllocation = ProjectUserAllocationMother::create(50);

        $projectUser->update($newRoleId, $newAllocation);

        $events = $projectUser->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ProjectUserWasUpdated::class, $events[0]);
        $this->assertTrue($newRoleId->equals($projectUser->roleId()));
        $this->assertSame(50, $projectUser->allocation()->value());
    }

    public function test_GivenActiveProjectUser_WhenDeactivate_ThenDeactivatedEventRecorded(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();

        $projectUser->deactivate();

        $events = $projectUser->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ProjectUserWasDeactivated::class, $events[0]);
        $this->assertFalse($projectUser->isActive());
    }

    public function test_GivenInactiveProjectUser_WhenDeactivateAgain_ThenNoEventRecorded(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();
        $projectUser->deactivate();
        $projectUser->pullEvents();

        // Idempotent: no extra event
        $projectUser->deactivate();
        $this->assertEmpty($projectUser->pullEvents());
    }

    public function test_GivenInactiveProjectUser_WhenActivate_ThenActivatedEventRecorded(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();
        $projectUser->deactivate();
        $projectUser->pullEvents();

        $projectUser->activate();

        $events = $projectUser->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ProjectUserWasActivated::class, $events[0]);
        $this->assertTrue($projectUser->isActive());
    }

    public function test_GivenActiveProjectUser_WhenToggle_ThenDeactivated(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();

        $projectUser->toggleActivation();

        $this->assertFalse($projectUser->isActive());
    }

    public function test_GivenInactiveProjectUser_WhenToggle_ThenActivated(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();
        $projectUser->deactivate();
        $projectUser->pullEvents();

        $projectUser->toggleActivation();

        $this->assertTrue($projectUser->isActive());
    }
}
