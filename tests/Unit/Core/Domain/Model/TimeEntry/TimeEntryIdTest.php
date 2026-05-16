<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\TimeEntry;

use App\Core\Domain\Exception\VO\InvalidTimeEntryIdException;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use PHPUnit\Framework\TestCase;

final class TimeEntryIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenCreated_ThenSuccess(): void
    {
        $id = new TimeEntryId('550e8400-e29b-41d4-a716-446655440000');
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', (string) $id);
    }

    public function test_GivenInvalidUuid_WhenCreated_ThenThrows(): void
    {
        $this->expectException(InvalidTimeEntryIdException::class);
        new TimeEntryId('not-a-uuid');
    }

    public function test_Generate_ReturnsValidId(): void
    {
        $id = TimeEntryId::generate();
        $this->assertNotEmpty((string) $id);
    }

    public function test_Equals_ReturnsTrue_ForSameValue(): void
    {
        $a = new TimeEntryId('550e8400-e29b-41d4-a716-446655440000');
        $b = new TimeEntryId('550e8400-e29b-41d4-a716-446655440000');
        $this->assertTrue($a->equals($b));
    }
}
