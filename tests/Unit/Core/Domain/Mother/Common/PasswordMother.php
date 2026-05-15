<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Common;

use App\Core\Domain\Model\VO\Common\Password;

final class PasswordMother
{
    public static function create(?string $hashedValue = null): Password
    {
        return new Password($hashedValue ?? '$2y$10$hashedpasswordvalue1234567890abc');
    }
}
