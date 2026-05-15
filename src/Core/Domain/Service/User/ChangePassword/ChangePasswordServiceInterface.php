<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\ChangePassword;

use App\Core\Domain\Model\VO\User\UserId;

interface ChangePasswordServiceInterface
{
    public function __invoke(UserId $id, string $oldPassword, string $newPassword): void;
}
