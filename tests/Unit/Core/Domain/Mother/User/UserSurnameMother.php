<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\User;

use App\Core\Domain\Model\VO\User\UserSurname;

final class UserSurnameMother
{
    public static function create(?string $value = null): UserSurname
    {
        return new UserSurname($value ?? 'Smith');
    }
}
