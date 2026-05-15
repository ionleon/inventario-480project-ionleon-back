<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\User;

use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Common\EmailMother;
use App\Tests\Unit\Core\Domain\Mother\Common\PasswordMother;

final class UserMother
{
    public static function create(
        ?UserId $id = null,
        ?Email $email = null,
        ?UserName $name = null,
        ?UserSurname $surname = null,
        ?Password $password = null,
        SystemRole $role = SystemRole::EMPLOYEE,
    ): User {
        return User::create(
            id: $id ?? UserIdMother::create(),
            email: $email ?? EmailMother::create(),
            name: $name ?? UserNameMother::create(),
            surname: $surname ?? UserSurnameMother::create(),
            password: $password ?? PasswordMother::create(),
            role: $role,
        );
    }
}
