<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Link;

use App\Core\Domain\Exception\VO\InvalidLinkIdException;
use App\Core\Domain\Model\VO\Link\LinkId;
use PHPUnit\Framework\TestCase;

final class LinkIdTest extends TestCase
{
    public function test_GivenValidUuid_WhenCreate_ThenLinkIdCreated(): void
    {
        $id = LinkId::generate();
        $this->assertInstanceOf(LinkId::class, $id);
    }

    public function test_GivenInvalidUuid_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidLinkIdException::class);
        new LinkId('not-a-uuid');
    }

    public function test_GivenTwoSameIds_WhenEquals_ThenReturnsTrue(): void
    {
        $value = LinkId::generate()->__toString();
        $id1 = new LinkId($value);
        $id2 = new LinkId($value);
        $this->assertTrue($id1->equals($id2));
    }

    public function test_GivenTwoDifferentIds_WhenEquals_ThenReturnsFalse(): void
    {
        $id1 = LinkId::generate();
        $id2 = LinkId::generate();
        $this->assertFalse($id1->equals($id2));
    }
}
