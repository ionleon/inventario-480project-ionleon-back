<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\RefreshToken;

use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;

final class RefreshTokenValueMother
{
    public static function create(string $value = 'abc123refreshtoken'): RefreshTokenValue
    {
        return new RefreshTokenValue($value);
    }

    public static function random(): RefreshTokenValue
    {
        return new RefreshTokenValue(bin2hex(random_bytes(32)));
    }
}
