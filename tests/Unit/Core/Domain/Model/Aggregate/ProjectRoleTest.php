<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\Event\ProjectRole\ProjectRoleWasCreated;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleNameMother;
use PHPUnit\Framework\TestCase;

final class ProjectRoleTest extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenInstanceWithCreatedEvent(): void
    {
        $projectRole = ProjectRoleMother::create();
        $events = $projectRole->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(ProjectRoleWasCreated::class, $events[0]);
    }

    public function test_GivenProjectRole_WhenGetters_ThenReturnExpectedValues(): void
    {
        $projectRole = ProjectRole::create(
            id: ProjectRoleIdMother::create('00000000-0000-4000-8000-000000000001'),
            name: ProjectRoleNameMother::create('Developer'),
        );

        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $projectRole->id());
        self::assertSame('Developer', (string) $projectRole->name());
    }
}
