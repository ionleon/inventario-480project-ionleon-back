<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\RefreshToken;

use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;
use PHPUnit\Framework\TestCase;

final class RefreshTokenValueTest extends TestCase
{
    public function test_GivenValidString_WhenConstruct_ThenOk(): void
    {
        $vo = new RefreshTokenValue('sometoken123');
        self::assertSame('sometoken123', (string) $vo);
    }

    public function test_GivenEmptyString_WhenConstruct_ThenException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RefreshTokenValue('');
    }

    public function test_GivenWhitespaceOnly_WhenConstruct_ThenException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RefreshTokenValue('   ');
    }

    public function test_GivenSameValues_WhenEquals_ThenTrue(): void
    {
        $a = new RefreshTokenValue('abc');
        $b = new RefreshTokenValue('abc');
        self::assertTrue($a->equals($b));
    }

    public function test_GivenDifferentValues_WhenEquals_ThenFalse(): void
    {
        $a = new RefreshTokenValue('abc');
        $b = new RefreshTokenValue('xyz');
        self::assertFalse($a->equals($b));
    }
}
