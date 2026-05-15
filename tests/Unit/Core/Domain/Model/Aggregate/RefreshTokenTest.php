<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\RefreshToken;
use App\Core\Domain\Model\Event\RefreshToken\RefreshTokenWasIssued;
use App\Core\Domain\Model\Event\RefreshToken\RefreshTokenWasRevoked;
use App\Tests\Unit\Core\Domain\Mother\RefreshToken\RefreshTokenExpiresAtMother;
use App\Tests\Unit\Core\Domain\Mother\RefreshToken\RefreshTokenMother;
use App\Tests\Unit\Core\Domain\Mother\RefreshToken\RefreshTokenValueMother;
use PHPUnit\Framework\TestCase;

final class RefreshTokenTest extends TestCase
{
    public function test_GivenValidParams_WhenIssue_ThenInstanceWithIssuedEvent(): void
    {
        $token = RefreshTokenMother::create();
        $events = $token->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(RefreshTokenWasIssued::class, $events[0]);
    }

    public function test_GivenFreshToken_WhenIsValid_ThenTrue(): void
    {
        $token = RefreshToken::issue(
            value: RefreshTokenValueMother::create('mytoken'),
            username: 'user@example.com',
            expiresAt: RefreshTokenExpiresAtMother::future(3600),
        );
        $token->pullEvents();

        self::assertTrue($token->isValid());
    }

    public function test_GivenExpiredToken_WhenIsValid_ThenFalse(): void
    {
        $token = RefreshTokenMother::expired();
        $token->pullEvents();

        self::assertFalse($token->isValid());
    }

    public function test_GivenToken_WhenRevoke_ThenRevokedEventAndIsInvalid(): void
    {
        $token = RefreshTokenMother::create();
        $token->pullEvents();

        $token->revoke();
        $events = $token->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(RefreshTokenWasRevoked::class, $events[0]);
        self::assertFalse($token->isValid());
    }

    public function test_GivenToken_WhenGetters_ThenReturnExpectedValues(): void
    {
        $value = RefreshTokenValueMother::create('specifictoken');
        $token = RefreshToken::issue(
            value: $value,
            username: 'test@example.com',
            expiresAt: RefreshTokenExpiresAtMother::future(),
        );

        self::assertSame('specifictoken', $token->getRefreshToken());
        self::assertSame('test@example.com', $token->getUsername());
        self::assertNotNull($token->getValid());
        self::assertNull($token->getId());
        self::assertSame('specifictoken', (string) $token);
    }
}
