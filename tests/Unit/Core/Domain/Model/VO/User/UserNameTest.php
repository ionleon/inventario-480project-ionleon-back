<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\User;

use App\Core\Domain\Exception\VO\InvalidUserNameException;
use App\Core\Domain\Model\VO\User\UserName;
use PHPUnit\Framework\TestCase;

final class UserNameTest extends TestCase
{
    public function test_GivenValidName_WhenConstruct_ThenCreatesInstance(): void
    {
        $name = new UserName('Alice');
        self::assertSame('Alice', (string) $name);
    }

    public function test_GivenNameWithSpaces_WhenConstruct_ThenTrimmed(): void
    {
        $name = new UserName('  Bob  ');
        self::assertSame('Bob', (string) $name);
    }

    public function test_GivenTooShortName_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidUserNameException::class);
        new UserName('Al');
    }

    public function test_GivenTooLongName_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidUserNameException::class);
        new UserName(str_repeat('A', 101));
    }

    public function test_GivenEmptyString_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidUserNameException::class);
        new UserName('');
    }

    public function test_GivenMinimumName_WhenConstruct_ThenCreatesInstance(): void
    {
        $name = new UserName('Ali');
        self::assertSame('Ali', (string) $name);
    }
}
