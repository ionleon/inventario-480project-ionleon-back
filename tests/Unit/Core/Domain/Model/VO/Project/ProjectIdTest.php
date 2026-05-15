<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Project;

use App\Core\Domain\Exception\VO\InvalidProjectIdException;
use App\Core\Domain\Model\VO\Project\ProjectId;
use PHPUnit\Framework\TestCase;

final class ProjectIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenCreate_ThenProjectIdCreated(): void
    {
        $id = ProjectId::generate();
        $this->assertInstanceOf(ProjectId::class, $id);
    }

    public function test_GivenInvalidUuid_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidProjectIdException::class);
        new ProjectId('not-a-uuid');
    }

    public function test_GivenTwoSameIds_WhenEquals_ThenReturnsTrue(): void
    {
        $value = ProjectId::generate()->__toString();
        $id1 = new ProjectId($value);
        $id2 = new ProjectId($value);
        $this->assertTrue($id1->equals($id2));
    }
}
