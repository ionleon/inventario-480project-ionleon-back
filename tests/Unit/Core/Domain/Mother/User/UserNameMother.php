<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\User;

use App\Core\Domain\Model\VO\User\UserName;

final class UserNameMother
{
    public static function create(?string $value = null): UserName
    {
        return new UserName($value ?? 'Alice');
    }
}
