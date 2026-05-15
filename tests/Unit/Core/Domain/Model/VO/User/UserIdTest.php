<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\User;

use App\Core\Domain\Exception\VO\InvalidUserIdException;
use App\Core\Domain\Model\VO\User\UserId;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenCreatesInstance(): void
    {
        $id = new UserId('00000000-0000-4000-8000-000000000001');
        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $id);
    }

    public function test_GivenInvalidUuid_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidUserIdException::class);
        new UserId('not-a-uuid');
    }

    public function test_GivenNoArgs_WhenGenerate_ThenCreatesValidUuid(): void
    {
        $id = UserId::generate();
        self::assertInstanceOf(UserId::class, $id);
        self::assertMatchesRegularExpression('/^[0-9a-f-]{36}$/i', (string) $id);
    }

    public function test_GivenSameValue_WhenEquals_ThenTrue(): void
    {
        $a = new UserId('00000000-0000-4000-8000-000000000001');
        $b = new UserId('00000000-0000-4000-8000-000000000001');
        self::assertTrue($a->equals($b));
    }

    public function test_GivenDifferentValue_WhenEquals_ThenFalse(): void
    {
        $a = new UserId('00000000-0000-4000-8000-000000000001');
        $b = new UserId('00000000-0000-4000-8000-000000000002');
        self::assertFalse($a->equals($b));
    }
}
