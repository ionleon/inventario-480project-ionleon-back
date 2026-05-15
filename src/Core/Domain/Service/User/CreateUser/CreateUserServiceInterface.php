<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\CreateUser;

use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Shared\Domain\Enum\SystemRole;

interface CreateUserServiceInterface
{
    public function __invoke(
        UserId $id,
        Email $email,
        UserName $name,
        UserSurname $surname,
        Password $password,
        SystemRole $role,
    ): User;
}
