<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\ProjectUser;

use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Service\ProjectUser\ToggleProjectUserActivation\ToggleProjectUserActivationService;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserIdMother;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use PHPUnit\Framework\TestCase;

final class ToggleProjectUserActivationServiceTest extends TestCase
{
    public function test_GivenActiveProjectUser_WhenInvoke_ThenDeactivated(): void
    {
        $projectUser = ProjectUserMother::create();
        $projectUser->pullEvents();

        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneOrFail')->willReturn($projectUser);

        $service = new ToggleProjectUserActivationService($puRepo);
        $result = $service(ProjectUserIdMother::create());

        $this->assertFalse($result->isActive());
    }

    public function test_GivenNotFoundProjectUser_WhenInvoke_ThenThrowsException(): void
    {
        $puRepo = $this->createMock(ProjectUserRepository::class);
        $puRepo->method('findOneOrFail')->willThrowException(new ProjectUserNotFoundException());

        $this->expectException(ProjectUserNotFoundException::class);

        (new ToggleProjectUserActivationService($puRepo))(ProjectUserIdMother::create());
    }
}
