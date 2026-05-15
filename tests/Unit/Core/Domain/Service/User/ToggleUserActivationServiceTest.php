<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\User;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\User\ToggleUserActivation\ToggleUserActivationService;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use PHPUnit\Framework\TestCase;

final class ToggleUserActivationServiceTest extends TestCase
{
    public function test_GivenActiveUser_WhenInvoke_ThenUserIsDeactivated(): void
    {
        $user = UserMother::create();
        self::assertTrue($user->isActive());

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneOrFail')->willReturn($user);

        (new ToggleUserActivationService($repo))(UserIdMother::create());

        self::assertFalse($user->isActive());
    }

    public function test_GivenNonExistentUser_WhenInvoke_ThenThrowsUserNotFoundException(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new UserNotFoundException());

        $this->expectException(UserNotFoundException::class);

        (new ToggleUserActivationService($repo))(UserIdMother::create());
    }
}
