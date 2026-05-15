<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\User;

use App\Core\Domain\Exception\VO\InvalidUserSurnameException;
use App\Core\Domain\Model\VO\User\UserSurname;
use PHPUnit\Framework\TestCase;

final class UserSurnameTest extends TestCase
{
    public function test_GivenValidSurname_WhenConstruct_ThenCreatesInstance(): void
    {
        $surname = new UserSurname('Smith');
        self::assertSame('Smith', (string) $surname);
    }

    public function test_GivenSingleChar_WhenConstruct_ThenCreatesInstance(): void
    {
        $surname = new UserSurname('S');
        self::assertSame('S', (string) $surname);
    }

    public function test_GivenEmptyAfterTrim_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidUserSurnameException::class);
        new UserSurname('   ');
    }

    public function test_GivenTooLongSurname_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidUserSurnameException::class);
        new UserSurname(str_repeat('S', 101));
    }

    public function test_GivenSurnameWithSpaces_WhenConstruct_ThenTrimmed(): void
    {
        $surname = new UserSurname('  Jones  ');
        self::assertSame('Jones', (string) $surname);
    }
}
