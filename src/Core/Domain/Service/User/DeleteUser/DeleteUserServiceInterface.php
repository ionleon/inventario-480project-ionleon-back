<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\DeleteUser;

use App\Core\Domain\Model\VO\User\UserId;

interface DeleteUserServiceInterface
{
    public function __invoke(UserId $id): void;
}
