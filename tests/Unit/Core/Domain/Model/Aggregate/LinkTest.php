<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\Event\Link\LinkWasCreated;
use App\Core\Domain\Model\Event\Link\LinkWasDeleted;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkIdMother;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkMother;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkUrlMother;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    public function test_GivenValidData_WhenCreate_ThenLinkCreatedWithEvent(): void
    {
        $id = LinkIdMother::create();
        $projectId = ProjectIdMother::create();
        $url = LinkUrlMother::create();

        $link = Link::create($id, $projectId, $url);

        $this->assertTrue($id->equals($link->id()));
        $this->assertTrue($projectId->equals($link->projectId()));
        $this->assertNull($link->label());

        $events = $link->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(LinkWasCreated::class, $events[0]);
    }

    public function test_GivenLink_WhenDelete_ThenLinkWasDeletedEventRecorded(): void
    {
        $link = LinkMother::create();
        $link->pullEvents(); // clear creation event

        $link->delete();

        $events = $link->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(LinkWasDeleted::class, $events[0]);
    }

    public function test_GivenLinkWithLabel_WhenCreate_ThenLabelIsSet(): void
    {
        $label = new \App\Core\Domain\Model\VO\Link\LinkLabel('Production');
        $link = LinkMother::create(label: $label);

        $this->assertNotNull($link->label());
        $this->assertSame('Production', (string) $link->label());
    }
}
