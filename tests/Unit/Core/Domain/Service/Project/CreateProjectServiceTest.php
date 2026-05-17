<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Project;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Exception\Project\DuplicatedProjectNameException;
use App\Core\Domain\Exception\Project\InvalidProjectDateRangeException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\Project\CreateProject\CreateProjectService;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectEndDateMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectNameMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectStartDateMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CreateProjectServiceTest extends TestCase
{
    public function test_GivenValidData_WhenInvoke_ThenProjectIsAdded(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneByName')->willReturn(null);
        $projectRepo->expects(self::once())->method('add');

        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willReturn(ClientMother::create());

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('findOneOrFail')->willReturn(UserMother::create());

        $techRepo = $this->createMock(TechnologyRepository::class);

        $service = new CreateProjectService($projectRepo, $clientRepo, $userRepo, $techRepo);
        $project = $service(
            ProjectIdMother::create(),
            ProjectNameMother::create(),
            null,
            ClientIdMother::create(),
            UserIdMother::create(),
            [],
            ProjectStartDateMother::create(),
            null,
        );

        $this->assertInstanceOf(\App\Core\Domain\Model\Aggregate\Project::class, $project);
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrowsException(): void
    {
        $existing = ProjectMother::create();
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneByName')->willReturn($existing);
        $projectRepo->expects(self::never())->method('add');

        $clientRepo = $this->createMock(ClientRepository::class);
        $userRepo = $this->createMock(UserRepository::class);
        $techRepo = $this->createMock(TechnologyRepository::class);

        $this->expectException(DuplicatedProjectNameException::class);

        (new CreateProjectService($projectRepo, $clientRepo, $userRepo, $techRepo))(
            ProjectIdMother::create(),
            ProjectNameMother::create(),
            null,
            ClientIdMother::create(),
            UserIdMother::create(),
            [],
            null,
            null,
        );
    }

    public function test_GivenClientNotFound_WhenInvoke_ThenThrowsException(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneByName')->willReturn(null);

        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willThrowException(new ClientNotFoundException());

        $userRepo = $this->createMock(UserRepository::class);
        $techRepo = $this->createMock(TechnologyRepository::class);

        $this->expectException(ClientNotFoundException::class);

        (new CreateProjectService($projectRepo, $clientRepo, $userRepo, $techRepo))(
            ProjectIdMother::create(),
            ProjectNameMother::create(),
            null,
            ClientIdMother::create(),
            UserIdMother::create(),
            [],
            null,
            null,
        );
    }

    public function test_GivenEndDateBeforeStartDate_WhenInvoke_ThenThrowsException(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneByName')->willReturn(null);

        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willReturn(ClientMother::create());

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('findOneOrFail')->willReturn(UserMother::create());

        $techRepo = $this->createMock(TechnologyRepository::class);

        $this->expectException(InvalidProjectDateRangeException::class);

        (new CreateProjectService($projectRepo, $clientRepo, $userRepo, $techRepo))(
            ProjectIdMother::create(),
            ProjectNameMother::create(),
            null,
            ClientIdMother::create(),
            UserIdMother::create(),
            [],
            ProjectStartDateMother::create(new DateTimeImmutable('2026-12-31')),
            ProjectEndDateMother::create(new DateTimeImmutable('2026-01-01')),
        );
    }
}
