<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\User;

use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\User\DeleteUser\DeleteUserService;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use PHPUnit\Framework\TestCase;

final class DeleteUserServiceTest extends TestCase
{
    public function test_GivenExistingUser_WhenInvoke_ThenUserIsRemoved(): void
    {
        $user = UserMother::create();
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneOrFail')->willReturn($user);
        $repo->expects(self::once())->method('remove')->with($user);

        (new DeleteUserService($repo))(UserIdMother::create());
    }

    public function test_GivenNonExistentUser_WhenInvoke_ThenThrowsUserNotFoundException(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new UserNotFoundException());

        $this->expectException(UserNotFoundException::class);

        (new DeleteUserService($repo))(UserIdMother::create());
    }
}
