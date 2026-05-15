<?php

namespace App\UserManagement\Application\ChangePassword;

final readonly class ChangePasswordCommand
{
    public function __construct(
      public string $userId,
      public string $oldPassword,
      public string $newPassword
    ) {}

}
