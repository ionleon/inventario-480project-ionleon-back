<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\User;

use App\Core\Domain\Exception\User\DuplicatedUserEmailException;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Service\User\CreateUser\CreateUserService;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Common\EmailMother;
use App\Tests\Unit\Core\Domain\Mother\Common\PasswordMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserNameMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserSurnameMother;
use PHPUnit\Framework\TestCase;

final class CreateUserServiceTest extends TestCase
{
    public function test_GivenUniqueEmail_WhenInvoke_ThenUserIsAdded(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneByEmail')->willReturn(null);
        $repo->expects(self::once())->method('add');

        $service = new CreateUserService($repo);
        $service(
            UserIdMother::create(),
            EmailMother::create(),
            UserNameMother::create(),
            UserSurnameMother::create(),
            PasswordMother::create(),
            SystemRole::EMPLOYEE,
        );
    }

    public function test_GivenDuplicatedEmail_WhenInvoke_ThenThrowsDuplicatedEmailException(): void
    {
        $existing = UserMother::create();
        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneByEmail')->willReturn($existing);
        $repo->expects(self::never())->method('add');

        $this->expectException(DuplicatedUserEmailException::class);

        (new CreateUserService($repo))(
            UserIdMother::create(),
            EmailMother::create(),
            UserNameMother::create(),
            UserSurnameMother::create(),
            PasswordMother::create(),
            SystemRole::EMPLOYEE,
        );
    }
}
