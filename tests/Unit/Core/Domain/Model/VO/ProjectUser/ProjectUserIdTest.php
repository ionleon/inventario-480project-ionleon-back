<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\ProjectUser;

use App\Core\Domain\Exception\VO\InvalidProjectUserIdException;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use PHPUnit\Framework\TestCase;

final class ProjectUserIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenCreated(): void
    {
        $id = ProjectUserId::generate();
        $this->assertNotEmpty((string) $id);
    }

    public function test_GivenInvalidUuid_WhenConstruct_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectUserIdException::class);
        new ProjectUserId('not-a-uuid');
    }

    public function test_GivenSameValue_WhenEquals_ThenReturnsTrue(): void
    {
        $id = ProjectUserId::generate();
        $id2 = new ProjectUserId((string) $id);
        $this->assertTrue($id->equals($id2));
    }

    public function test_GivenDifferentValues_WhenEquals_ThenReturnsFalse(): void
    {
        $id1 = ProjectUserId::generate();
        $id2 = ProjectUserId::generate();
        $this->assertFalse($id1->equals($id2));
    }
}
