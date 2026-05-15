<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\ResetPassword;

final readonly class ResetPasswordRequest
{
    public function __construct(
        public string $newPassword,
    ) {}
}
