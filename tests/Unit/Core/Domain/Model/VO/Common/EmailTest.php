<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Common;

use App\Core\Domain\Exception\VO\InvalidEmailException;
use App\Core\Domain\Model\VO\Common\Email;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function test_GivenValidEmail_WhenConstruct_ThenCreatesInstance(): void
    {
        $email = new Email('alice@example.com');
        self::assertSame('alice@example.com', (string) $email);
    }

    public function test_GivenUppercaseEmail_WhenConstruct_ThenNormalizesToLowercase(): void
    {
        $email = new Email('ALICE@EXAMPLE.COM');
        self::assertSame('alice@example.com', (string) $email);
    }

    public function test_GivenInvalidEmail_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);
        new Email('not-an-email');
    }

    public function test_GivenTooLongEmail_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);
        new Email(str_repeat('a', 140) . '@b.com');
    }

    public function test_GivenSameEmail_WhenEquals_ThenTrue(): void
    {
        $a = new Email('alice@example.com');
        $b = new Email('Alice@Example.COM');
        self::assertTrue($a->equals($b));
    }
}
