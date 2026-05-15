<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Link;

use App\Core\Domain\Exception\VO\InvalidLinkUrlException;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use PHPUnit\Framework\TestCase;

final class LinkUrlTest extends TestCase
{
    public function test_GivenValidUrl_WhenCreate_ThenLinkUrlCreated(): void
    {
        $url = new LinkUrl('https://example.com/path');
        $this->assertSame('https://example.com/path', (string) $url);
    }

    public function test_GivenInvalidUrl_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidLinkUrlException::class);
        new LinkUrl('not-a-valid-url');
    }

    public function test_GivenUrlExceedingMaxLength_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(InvalidLinkUrlException::class);
        new LinkUrl('https://example.com/' . str_repeat('a', 490));
    }

    public function test_GivenTwoSameUrls_WhenEquals_ThenReturnsTrue(): void
    {
        $url1 = new LinkUrl('https://example.com');
        $url2 = new LinkUrl('https://example.com');
        $this->assertTrue($url1->equals($url2));
    }
}
