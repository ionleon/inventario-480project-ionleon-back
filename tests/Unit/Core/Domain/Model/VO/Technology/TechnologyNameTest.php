<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Technology;

use App\Core\Domain\Exception\VO\InvalidTechnologyNameException;
use App\Core\Domain\Model\VO\Technology\TechnologyName;
use PHPUnit\Framework\TestCase;

final class TechnologyNameTest extends TestCase
{
    public function test_GivenValidName_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $name = new TechnologyName('PHP');
        self::assertSame('PHP', (string) $name);
    }

    public function test_GivenNameWithWhitespace_WhenConstruct_ThenTrimmed(): void
    {
        $name = new TechnologyName('  PHP  ');
        self::assertSame('PHP', (string) $name);
    }

    public function test_GivenEmptyName_WhenConstruct_ThenThrowsInvalidTechnologyNameException(): void
    {
        $this->expectException(InvalidTechnologyNameException::class);
        new TechnologyName('');
    }

    public function test_GivenTooShortName_WhenConstruct_ThenThrowsInvalidTechnologyNameException(): void
    {
        $this->expectException(InvalidTechnologyNameException::class);
        new TechnologyName('A');
    }

    public function test_GivenTooLongName_WhenConstruct_ThenThrowsInvalidTechnologyNameException(): void
    {
        $this->expectException(InvalidTechnologyNameException::class);
        new TechnologyName(str_repeat('A', 81));
    }

    public function test_GivenSameValue_WhenEquals_ThenReturnsTrue(): void
    {
        $name1 = new TechnologyName('PHP');
        $name2 = new TechnologyName('PHP');
        self::assertTrue($name1->equals($name2));
    }

    public function test_GivenDifferentValues_WhenEquals_ThenReturnsFalse(): void
    {
        $name1 = new TechnologyName('PHP');
        $name2 = new TechnologyName('JavaScript');
        self::assertFalse($name1->equals($name2));
    }
}
