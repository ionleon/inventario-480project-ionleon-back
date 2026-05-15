<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\VO\Link;

use App\Core\Domain\Model\VO\Link\LinkLabel;
use PHPUnit\Framework\TestCase;

final class LinkLabelTest extends TestCase
{
    public function test_GivenValidLabel_WhenCreate_ThenLinkLabelCreated(): void
    {
        $label = new LinkLabel('Production');
        $this->assertSame('Production', (string) $label);
    }

    public function test_GivenEmptyLabel_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new LinkLabel('');
    }

    public function test_GivenLabelExceedingMaxLength_WhenCreate_ThenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new LinkLabel(str_repeat('a', 101));
    }

    public function test_GivenLabelWithWhitespace_WhenCreate_ThenTrimmed(): void
    {
        $label = new LinkLabel('  Staging  ');
        $this->assertSame('Staging', (string) $label);
    }
}
