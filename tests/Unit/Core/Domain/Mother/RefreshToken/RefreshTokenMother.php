<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\RefreshToken;

use App\Core\Domain\Model\Aggregate\RefreshToken;

final class RefreshTokenMother
{
    public static function create(
        ?string $value = null,
        string $username = 'user@example.com',
    ): RefreshToken {
        return RefreshToken::issue(
            value: RefreshTokenValueMother::create($value ?? 'abc123refreshtoken'),
            username: $username,
            expiresAt: RefreshTokenExpiresAtMother::future(),
        );
    }

    public static function expired(): RefreshToken
    {
        return RefreshToken::issue(
            value: RefreshTokenValueMother::random(),
            username: 'user@example.com',
            expiresAt: RefreshTokenExpiresAtMother::past(),
        );
    }
}
