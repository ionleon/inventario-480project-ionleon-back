<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Contact;

use App\Core\Domain\Exception\VO\InvalidContactNameException;
use App\Core\Domain\Model\VO\Contact\ContactName;
use PHPUnit\Framework\TestCase;

final class ContactNameTest extends TestCase
{
    public function test_GivenValidName_WhenConstruct_ThenSuccess(): void
    {
        $name = new ContactName('John Doe');
        self::assertSame('John Doe', (string) $name);
    }

    public function test_GivenTooShortName_WhenConstruct_ThenThrowsInvalidContactNameException(): void
    {
        $this->expectException(InvalidContactNameException::class);
        new ContactName('A');
    }

    public function test_GivenEmptyName_WhenConstruct_ThenThrowsInvalidContactNameException(): void
    {
        $this->expectException(InvalidContactNameException::class);
        new ContactName('');
    }

    public function test_GivenTooLongName_WhenConstruct_ThenThrowsInvalidContactNameException(): void
    {
        $this->expectException(InvalidContactNameException::class);
        new ContactName(str_repeat('A', 256));
    }

    public function test_GivenNameWithWhitespace_WhenConstruct_ThenTrimmed(): void
    {
        $name = new ContactName('  Jane Smith  ');
        self::assertSame('Jane Smith', (string) $name);
    }

    public function test_Equals_WhenSameValue_ThenTrue(): void
    {
        $a = new ContactName('John Doe');
        $b = new ContactName('John Doe');
        self::assertTrue($a->equals($b));
    }
}
