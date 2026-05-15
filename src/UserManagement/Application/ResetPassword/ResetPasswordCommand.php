<?php

namespace App\UserManagement\Application\ResetPassword;

class ResetPasswordCommand
{
    public function __construct(
        public string $userId,
        public string $newPassword
    ) {}

}
