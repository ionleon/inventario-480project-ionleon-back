<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\ChangePassword;

final readonly class ChangePasswordRequest
{
    public function __construct(
        public string $oldPassword,
        public string $newPassword,
    ) {}
}
