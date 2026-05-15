<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\ProjectRole;

use App\Core\Domain\Exception\VO\InvalidProjectRoleIdException;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use PHPUnit\Framework\TestCase;

final class ProjectRoleIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenConstruct_ThenInstanceIsCreated(): void
    {
        $id = new ProjectRoleId('00000000-0000-4000-8000-000000000000');
        self::assertSame('00000000-0000-4000-8000-000000000000', (string) $id);
    }

    public function test_GivenInvalidUuid_WhenConstruct_ThenThrowsInvalidProjectRoleIdException(): void
    {
        $this->expectException(InvalidProjectRoleIdException::class);
        new ProjectRoleId('not-a-uuid');
    }

    public function test_GivenNull_WhenGenerate_ThenInstanceIsCreated(): void
    {
        $id = ProjectRoleId::generate();
        self::assertNotEmpty((string) $id);
    }

    public function test_GivenSameValue_WhenEquals_ThenReturnsTrue(): void
    {
        $id1 = new ProjectRoleId('00000000-0000-4000-8000-000000000001');
        $id2 = new ProjectRoleId('00000000-0000-4000-8000-000000000001');
        self::assertTrue($id1->equals($id2));
    }

    public function test_GivenDifferentValues_WhenEquals_ThenReturnsFalse(): void
    {
        $id1 = new ProjectRoleId('00000000-0000-4000-8000-000000000001');
        $id2 = new ProjectRoleId('00000000-0000-4000-8000-000000000002');
        self::assertFalse($id1->equals($id2));
    }
}
