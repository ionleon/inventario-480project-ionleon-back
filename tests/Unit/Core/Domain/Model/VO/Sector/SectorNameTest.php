<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Sector;

use App\Core\Domain\Exception\VO\InvalidSectorNameException;
use App\Core\Domain\Model\VO\Sector\SectorName;
use PHPUnit\Framework\TestCase;

final class SectorNameTest extends TestCase
{
    public function test_GivenValidName_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $name = new SectorName('Technology');
        self::assertSame('Technology', (string) $name);
    }

    public function test_GivenNameWithSpaces_WhenConstruct_ThenValueIsTrimmed(): void
    {
        $name = new SectorName('  Finance  ');
        self::assertSame('Finance', (string) $name);
    }

    public function test_GivenEmptyString_WhenConstruct_ThenThrowsInvalidSectorNameException(): void
    {
        $this->expectException(InvalidSectorNameException::class);
        new SectorName('');
    }

    public function test_GivenOnlySpaces_WhenConstruct_ThenThrowsInvalidSectorNameException(): void
    {
        $this->expectException(InvalidSectorNameException::class);
        new SectorName('   ');
    }

    public function test_GivenOneCharName_WhenConstruct_ThenThrowsInvalidSectorNameException(): void
    {
        $this->expectException(InvalidSectorNameException::class);
        new SectorName('A');
    }

    public function test_GivenTwoCharName_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $name = new SectorName('IT');
        self::assertSame('IT', (string) $name);
    }

    public function test_GivenNameExceedingMaxLength_WhenConstruct_ThenThrowsInvalidSectorNameException(): void
    {
        $this->expectException(InvalidSectorNameException::class);
        new SectorName(str_repeat('A', 101));
    }

    public function test_GivenSameValue_WhenEquals_ThenTrue(): void
    {
        $a = new SectorName('Finance');
        $b = new SectorName('Finance');
        self::assertTrue($a->equals($b));
    }

    public function test_GivenDifferentValue_WhenEquals_ThenFalse(): void
    {
        $a = new SectorName('Finance');
        $b = new SectorName('Technology');
        self::assertFalse($a->equals($b));
    }
}
