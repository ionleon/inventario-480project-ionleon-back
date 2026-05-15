<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\RefreshToken;

use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenExpiresAt;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class RefreshTokenExpiresAtTest extends TestCase
{
    public function test_GivenFutureDate_WhenIsInFuture_ThenTrue(): void
    {
        $vo = new RefreshTokenExpiresAt(new DateTimeImmutable('+1 hour'));
        self::assertTrue($vo->isInFuture());
    }

    public function test_GivenPastDate_WhenIsInFuture_ThenFalse(): void
    {
        $vo = new RefreshTokenExpiresAt(new DateTimeImmutable('-1 second'));
        self::assertFalse($vo->isInFuture());
    }

    public function test_GivenDate_WhenValue_ThenReturnsSameInstance(): void
    {
        $date = new DateTimeImmutable('+1 hour');
        $vo = new RefreshTokenExpiresAt($date);
        self::assertSame($date, $vo->value());
    }
}
