<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\UpdateUser;

use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;

interface UpdateUserServiceInterface
{
    public function __invoke(UserId $id, UserName $name, UserSurname $surname): User;
}
