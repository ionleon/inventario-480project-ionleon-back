<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\ProjectUser;

use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Service\ProjectUser\UpdateProjectUser\UpdateProjectUserService;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserAllocationMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use PHPUnit\Framework\TestCase;

final class UpdateProjectUserServiceTest extends TestCase
{
    public function test_GivenValidData_WhenInvoke_ThenProjectUserUpdated(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();

        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneOrFail')->willReturn($projectUser);

        $roleRepo = $this->createMock(ProjectRoleRepository::class);
        $roleRepo->method('findOneOrFail')->willReturn(ProjectRoleMother::create());

        $service = new UpdateProjectUserService($puRepo, $roleRepo);
        $result = $service(
            ProjectUserIdMother::create(),
            ProjectRoleIdMother::create(),
            ProjectUserAllocationMother::create(75),
        );

        $this->assertSame(75, $result->allocation()->value());
    }

    public function test_GivenNotFoundProjectUser_WhenInvoke_ThenThrowsException(): void
    {
        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneOrFail')->willThrowException(new ProjectUserNotFoundException());

        $roleRepo = $this->createMock(ProjectRoleRepository::class);

        $this->expectException(ProjectUserNotFoundException::class);

        (new UpdateProjectUserService($puRepo, $roleRepo))(
            ProjectUserIdMother::create(),
            ProjectRoleIdMother::create(),
            ProjectUserAllocationMother::create(),
        );
    }
}
