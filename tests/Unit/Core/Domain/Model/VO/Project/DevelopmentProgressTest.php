<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Project;

use App\Core\Domain\Model\VO\Project\DevelopmentProgress;
use PHPUnit\Framework\TestCase;

final class DevelopmentProgressTest extends TestCase
{
    public function test_GivenValidProgress_WhenCreate_ThenProgressCreated(): void
    {
        $progress = new DevelopmentProgress(50);
        $this->assertSame(50, $progress->value());
    }

    public function test_GivenZero_WhenCreate_ThenProgressCreated(): void
    {
        $progress = new DevelopmentProgress(0);
        $this->assertSame(0, $progress->value());
    }

    public function test_GivenHundred_WhenCreate_ThenProgressCreated(): void
    {
        $progress = new DevelopmentProgress(100);
        $this->assertSame(100, $progress->value());
    }

    public function test_GivenNegative_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DevelopmentProgress(-1);
    }

    public function test_GivenOver100_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DevelopmentProgress(101);
    }
}
