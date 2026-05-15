<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\ProjectRole;

use App\Core\Domain\Exception\ProjectRole\ProjectRoleNotFoundException;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Service\ProjectRole\DeleteProjectRole\DeleteProjectRoleService;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use PHPUnit\Framework\TestCase;

final class DeleteProjectRoleServiceTest extends TestCase
{
    public function test_GivenExistingProjectRole_WhenInvoke_ThenAggregateIsRemoved(): void
    {
        $projectRole = ProjectRoleMother::create();
        $repo = $this->createMock(ProjectRoleRepository::class);
        $repo->method('findOneOrFail')->willReturn($projectRole);
        $repo->expects(self::once())->method('remove');

        $service = new DeleteProjectRoleService($repo);
        $service(ProjectRoleIdMother::create());
    }

    public function test_GivenNonExistingProjectRole_WhenInvoke_ThenThrowsProjectRoleNotFoundException(): void
    {
        $repo = $this->createMock(ProjectRoleRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new ProjectRoleNotFoundException('test-id'));

        $this->expectException(ProjectRoleNotFoundException::class);

        (new DeleteProjectRoleService($repo))(ProjectRoleIdMother::create());
    }
}
