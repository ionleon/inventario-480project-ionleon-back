<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\ProjectRole;

use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;
use PHPUnit\Framework\TestCase;

final class ProjectRoleNameTest extends TestCase
{
    public function test_GivenValidName_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $name = new ProjectRoleName('Developer');
        self::assertSame('Developer', (string) $name);
    }

    public function test_GivenNameWithWhitespace_WhenConstruct_ThenTrimmed(): void
    {
        $name = new ProjectRoleName('  Developer  ');
        self::assertSame('Developer', (string) $name);
    }

    public function test_GivenEmptyName_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProjectRoleName('');
    }

    public function test_GivenTooShortName_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProjectRoleName('A');
    }

    public function test_GivenTooLongName_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProjectRoleName(str_repeat('A', 81));
    }

    public function test_GivenSameValue_WhenEquals_ThenReturnsTrue(): void
    {
        $name1 = new ProjectRoleName('Developer');
        $name2 = new ProjectRoleName('Developer');
        self::assertTrue($name1->equals($name2));
    }

    public function test_GivenDifferentValues_WhenEquals_ThenReturnsFalse(): void
    {
        $name1 = new ProjectRoleName('Developer');
        $name2 = new ProjectRoleName('Designer');
        self::assertFalse($name1->equals($name2));
    }
}
