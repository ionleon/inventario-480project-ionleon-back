<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\ProjectUser;

use App\Core\Domain\Exception\ProjectUser\DuplicatedProjectUserException;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\ProjectUser\AssignUserToProject\AssignUserToProjectService;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserAllocationMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use App\Core\Domain\Model\VO\User\UserId;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class AssignUserToProjectServiceTest extends TestCase
{
    public function test_GivenValidData_WhenInvoke_ThenProjectUserAdded(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneOrFail')->willReturn(ProjectMother::create());

        $userId = new UserId(Uuid::v4()->toRfc4122());

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('findOneOrFail')->willReturn(UserMother::create());

        $roleRepo = $this->createMock(ProjectRoleRepository::class);
        $roleRepo->method('findOneOrFail')->willReturn(ProjectRoleMother::create());

        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneByProjectAndUser')->willReturn(null);
        $puRepo->expects(self::once())->method('add');

        $service = new AssignUserToProjectService($puRepo, $projectRepo, $userRepo, $roleRepo);
        $result = $service(
            ProjectUserIdMother::create(),
            ProjectIdMother::create(),
            $userId,
            ProjectRoleIdMother::create(),
            ProjectUserAllocationMother::create(),
        );

        $this->assertTrue($result->isActive());
    }

    public function test_GivenActiveExistingAssignment_WhenInvoke_ThenThrowsDuplicatedException(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneOrFail')->willReturn(ProjectMother::create());

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('findOneOrFail')->willReturn(UserMother::create());

        $roleRepo = $this->createMock(ProjectRoleRepository::class);
        $roleRepo->method('findOneOrFail')->willReturn(ProjectRoleMother::create());

        $existing = ProjectUserMother::create();
        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneByProjectAndUser')->willReturn($existing);

        $this->expectException(DuplicatedProjectUserException::class);

        (new AssignUserToProjectService($puRepo, $projectRepo, $userRepo, $roleRepo))(
            ProjectUserIdMother::create(),
            ProjectIdMother::create(),
            new UserId(Uuid::v4()->toRfc4122()),
            ProjectRoleIdMother::create(),
            ProjectUserAllocationMother::create(),
        );
    }
}
