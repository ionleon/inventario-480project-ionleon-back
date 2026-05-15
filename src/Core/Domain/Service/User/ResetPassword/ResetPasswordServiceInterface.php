<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\User\ResetPassword;

use App\Core\Domain\Model\VO\User\UserId;

interface ResetPasswordServiceInterface
{
    public function __invoke(UserId $id, string $newPassword): void;
}
