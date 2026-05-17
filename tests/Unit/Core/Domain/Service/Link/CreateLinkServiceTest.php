<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Link;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Service\Link\CreateLink\CreateLinkService;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkIdMother;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkUrlMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectMother;
use PHPUnit\Framework\TestCase;

final class CreateLinkServiceTest extends TestCase
{
    public function test_GivenValidData_WhenInvoke_ThenLinkIsAdded(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneOrFail')->willReturn(ProjectMother::create());

        $linkRepo = $this->createMock(LinkRepository::class);
        $linkRepo->expects(self::once())->method('add');

        $service = new CreateLinkService($linkRepo, $projectRepo);
        $link = $service(
            LinkIdMother::create(),
            ProjectIdMother::create(),
            LinkUrlMother::create(),
            null,
        );

        $this->assertInstanceOf(\App\Core\Domain\Model\Aggregate\Link::class, $link);
    }

    public function test_GivenProjectNotFound_WhenInvoke_ThenThrowsException(): void
    {
        $projectRepo = $this->createMock(ProjectRepository::class);
        $projectRepo->method('findOneOrFail')->willThrowException(new ProjectNotFoundException());

        $linkRepo = $this->createMock(LinkRepository::class);
        $linkRepo->expects(self::never())->method('add');

        $this->expectException(ProjectNotFoundException::class);

        (new CreateLinkService($linkRepo, $projectRepo))(
            LinkIdMother::create(),
            ProjectIdMother::create(),
            LinkUrlMother::create(),
            null,
        );
    }
}
