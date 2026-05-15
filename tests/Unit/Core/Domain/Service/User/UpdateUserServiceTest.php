<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\User;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\User\UpdateUser\UpdateUserService;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserNameMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserSurnameMother;
use PHPUnit\Framework\TestCase;

final class UpdateUserServiceTest extends TestCase
{
    public function test_GivenExistingUser_WhenInvoke_ThenProfileIsUpdated(): void
    {
        $user = UserMother::create();
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneOrFail')->willReturn($user);

        (new UpdateUserService($repo))(
            UserIdMother::create(),
            UserNameMother::create('Bob'),
            UserSurnameMother::create('Jones'),
        );

        self::assertSame('Bob', (string) $user->name());
        self::assertSame('Jones', (string) $user->surname());
    }

    public function test_GivenNonExistentUser_WhenInvoke_ThenThrowsUserNotFoundException(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new UserNotFoundException());

        $this->expectException(UserNotFoundException::class);

        (new UpdateUserService($repo))(
            UserIdMother::create(),
            UserNameMother::create(),
            UserSurnameMother::create(),
        );
    }
}
