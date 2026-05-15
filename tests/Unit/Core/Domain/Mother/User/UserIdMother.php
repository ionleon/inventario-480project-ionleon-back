<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\User;

use App\Core\Domain\Model\VO\User\UserId;

final class UserIdMother
{
    public static function create(?string $value = null): UserId
    {
        return new UserId($value ?? UserId::generate()->__toString());
    }
}
