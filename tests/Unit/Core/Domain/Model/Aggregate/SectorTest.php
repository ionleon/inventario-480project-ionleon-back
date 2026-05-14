<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\Event\Sector\SectorWasCreated;
use App\Core\Domain\Model\Event\Sector\SectorWasUpdated;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorNameMother;
use PHPUnit\Framework\TestCase;

final class SectorTest extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenInstanceWithCreatedEvent(): void
    {
        $sector = SectorMother::create();
        $events = $sector->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(SectorWasCreated::class, $events[0]);
    }

    public function test_GivenSameName_WhenRename_ThenNoEventFired(): void
    {
        $name = SectorNameMother::create('Finance');
        $sector = SectorMother::create(name: $name);
        $sector->pullEvents(); // clear create event

        $sector->rename(SectorNameMother::create('Finance'));

        self::assertCount(0, $sector->pullEvents());
    }

    public function test_GivenDifferentName_WhenRename_ThenUpdatedEventFired(): void
    {
        $sector = SectorMother::create(name: SectorNameMother::create('Finance'));
        $sector->pullEvents(); // clear create event

        $sector->rename(SectorNameMother::create('Technology'));

        $events = $sector->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(SectorWasUpdated::class, $events[0]);
    }

    public function test_GivenSector_WhenGetters_ThenReturnExpectedValues(): void
    {
        $sector = Sector::create(
            id: \App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother::create('00000000-0000-4000-8000-000000000001'),
            name: SectorNameMother::create('Finance'),
        );

        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $sector->id());
        self::assertSame('Finance', (string) $sector->name());
    }
}
