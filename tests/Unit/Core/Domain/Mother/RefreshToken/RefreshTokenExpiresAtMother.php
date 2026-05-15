<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\RefreshToken;

use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenExpiresAt;
use DateTimeImmutable;

final class RefreshTokenExpiresAtMother
{
    public static function future(int $secondsFromNow = 3600): RefreshTokenExpiresAt
    {
        return new RefreshTokenExpiresAt(new DateTimeImmutable('+' . $secondsFromNow . ' seconds'));
    }

    public static function past(): RefreshTokenExpiresAt
    {
        return new RefreshTokenExpiresAt(new DateTimeImmutable('-1 second'));
    }
}
