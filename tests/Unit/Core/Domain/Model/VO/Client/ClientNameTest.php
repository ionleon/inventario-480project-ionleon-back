<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Client;

use App\Core\Domain\Exception\VO\InvalidClientNameException;
use App\Core\Domain\Model\VO\Client\ClientName;
use PHPUnit\Framework\TestCase;

final class ClientNameTest extends TestCase
{
    public function test_GivenValidName_WhenConstruct_ThenCreated(): void
    {
        $name = new ClientName('Acme Corp');
        self::assertSame('Acme Corp', (string) $name);
    }

    public function test_GivenNameWithWhitespace_WhenConstruct_ThenTrimmed(): void
    {
        $name = new ClientName('  Acme Corp  ');
        self::assertSame('Acme Corp', (string) $name);
    }

    public function test_GivenEmptyString_WhenConstruct_ThenThrows(): void
    {
        $this->expectException(InvalidClientNameException::class);
        new ClientName('');
    }

    public function test_GivenTooShortName_WhenConstruct_ThenThrows(): void
    {
        $this->expectException(InvalidClientNameException::class);
        new ClientName('A');
    }

    public function test_GivenTooLongName_WhenConstruct_ThenThrows(): void
    {
        $this->expectException(InvalidClientNameException::class);
        new ClientName(str_repeat('A', 121));
    }

    public function test_GivenMaxLengthName_WhenConstruct_ThenCreated(): void
    {
        $name = new ClientName(str_repeat('A', 120));
        self::assertSame(120, mb_strlen((string) $name));
    }

    public function test_GivenSameValue_WhenEquals_ThenTrue(): void
    {
        $a = new ClientName('Acme Corp');
        $b = new ClientName('Acme Corp');
        self::assertTrue($a->equals($b));
    }

    public function test_GivenDifferentValue_WhenEquals_ThenFalse(): void
    {
        $a = new ClientName('Acme Corp');
        $b = new ClientName('Beta Corp');
        self::assertFalse($a->equals($b));
    }
}
