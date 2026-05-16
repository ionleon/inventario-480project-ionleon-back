<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\TimeEntry;

use App\Core\Domain\Exception\TimeEntry\UserNotAssignedToProjectException;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\TimeEntry\CreateTimeEntry\CreateTimeEntryService;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryDateMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryHoursMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use App\Core\Domain\Model\VO\User\UserId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CreateTimeEntryServiceTest extends TestCase
{
    public function test_GivenValidData_WhenInvoke_ThenTimeEntryAdded(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneOrFail')->willReturn(ProjectMother::create());

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('findOneOrFail')->willReturn(UserMother::create());

        $projectUser = ProjectUserMother::create();
        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneByProjectAndUser')->willReturn($projectUser);

        $teRepo = $this->createMock(TimeEntryRepository::class);
        $teRepo->expects(self::once())->method('add');

        $service = new CreateTimeEntryService($teRepo, $projectRepo, $userRepo, $puRepo);

        $result = $service(
            TimeEntryIdMother::create(),
            ProjectIdMother::create(),
            new UserId(Uuid::v4()->toRfc4122()),
            TimeEntryDateMother::create(),
            TimeEntryHoursMother::create(),
            null,
        );

        $this->assertSame('8.00', $result->hours()->value());
    }

    public function test_GivenUserNotAssignedToProject_WhenInvoke_ThenThrows(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneOrFail')->willReturn(ProjectMother::create());

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('findOneOrFail')->willReturn(UserMother::create());

        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneByProjectAndUser')->willReturn(null);

        $teRepo = $this->createMock(TimeEntryRepository::class);

        $this->expectException(UserNotAssignedToProjectException::class);

        (new CreateTimeEntryService($teRepo, $projectRepo, $userRepo, $puRepo))(
            TimeEntryIdMother::create(),
            ProjectIdMother::create(),
            new UserId(Uuid::v4()->toRfc4122()),
            TimeEntryDateMother::create(),
            TimeEntryHoursMother::create(),
            null,
        );
    }
}
