<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Project;

use App\Core\Domain\Exception\VO\InvalidProjectNameException;
use App\Core\Domain\Model\VO\Project\ProjectName;
use PHPUnit\Framework\TestCase;

final class ProjectNameTest extends TestCase
{
    public function test_GivenValidName_WhenCreate_ThenNameCreated(): void
    {
        $name = new ProjectName('My Project');
        $this->assertSame('My Project', (string) $name);
    }

    public function test_GivenTooShortName_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectNameException::class);
        new ProjectName('A');
    }

    public function test_GivenTooLongName_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectNameException::class);
        new ProjectName(str_repeat('A', 151));
    }

    public function test_GivenNameWithSpaces_WhenCreate_ThenTrimsName(): void
    {
        $name = new ProjectName('  My Project  ');
        $this->assertSame('My Project', (string) $name);
    }
}
