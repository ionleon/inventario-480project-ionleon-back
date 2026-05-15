<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\ProjectUser;

use App\Core\Domain\Exception\VO\InvalidProjectUserAllocationException;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserAllocation;
use PHPUnit\Framework\TestCase;

final class ProjectUserAllocationTest extends TestCase
{
    public function test_GivenZero_WhenConstruct_ThenCreated(): void
    {
        $allocation = new ProjectUserAllocation(0);
        $this->assertSame(0, $allocation->value());
    }

    public function test_GivenHundred_WhenConstruct_ThenCreated(): void
    {
        $allocation = new ProjectUserAllocation(100);
        $this->assertSame(100, $allocation->value());
    }

    public function test_GivenNegative_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectUserAllocationException::class);
        new ProjectUserAllocation(-1);
    }

    public function test_GivenOver100_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectUserAllocationException::class);
        new ProjectUserAllocation(101);
    }

    public function test_GivenSameValue_WhenEquals_ThenReturnsTrue(): void
    {
        $a = new ProjectUserAllocation(50);
        $b = new ProjectUserAllocation(50);
        $this->assertTrue($a->equals($b));
    }
}
