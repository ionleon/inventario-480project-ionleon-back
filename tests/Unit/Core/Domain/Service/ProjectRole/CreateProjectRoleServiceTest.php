<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\ProjectRole;

use App\Core\Domain\Exception\ProjectRole\DuplicatedProjectRoleNameException;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Service\ProjectRole\CreateProjectRole\CreateProjectRoleService;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleNameMother;
use PHPUnit\Framework\TestCase;

final class CreateProjectRoleServiceTest extends TestCase
{
    public function test_GivenUniqueName_WhenInvoke_ThenAggregateIsAdded(): void
    {
        $repo = $this->createMock(ProjectRoleRepository::class);
        $repo->method('findOneByName')->willReturn(null);
        $repo->expects(self::once())->method('add');

        $service = new CreateProjectRoleService($repo);
        $service(ProjectRoleIdMother::create(), ProjectRoleNameMother::create());
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrowsDuplicatedProjectRoleNameException(): void
    {
        $existing = ProjectRoleMother::create();
        $repo = $this->createMock(ProjectRoleRepository::class);
        $repo->method('findOneByName')->willReturn($existing);
        $repo->expects(self::never())->method('add');

        $this->expectException(DuplicatedProjectRoleNameException::class);

        (new CreateProjectRoleService($repo))(
            ProjectRoleIdMother::create(),
            ProjectRoleNameMother::create(),
        );
    }
}
