<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\Event\Client\ClientWasActivated;
use App\Core\Domain\Model\Event\Client\ClientWasCreated;
use App\Core\Domain\Model\Event\Client\ClientWasDeactivated;
use App\Core\Domain\Model\Event\Client\ClientWasUpdated;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientNameMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenInstanceWithCreatedEvent(): void
    {
        $client = ClientMother::create();
        $events = $client->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(ClientWasCreated::class, $events[0]);
    }

    public function test_GivenNewClient_WhenCreate_ThenIsActive(): void
    {
        $client = ClientMother::create();

        self::assertTrue($client->isActive());
    }

    public function test_GivenClient_WhenGetters_ThenReturnExpectedValues(): void
    {
        $id = ClientIdMother::create('00000000-0000-4000-8000-000000000001');
        $name = ClientNameMother::create('Acme Corp');
        $sectorId = SectorIdMother::create('00000000-0000-4000-8000-000000000002');

        $client = Client::create(id: $id, name: $name, sectorId: $sectorId);

        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $client->id());
        self::assertSame('Acme Corp', (string) $client->name());
        self::assertSame('00000000-0000-4000-8000-000000000002', (string) $client->sectorId());
        self::assertTrue($client->isActive());
    }

    public function test_GivenClient_WhenUpdate_ThenUpdatedEventFired(): void
    {
        $client = ClientMother::create();
        $client->pullEvents(); // clear create event

        $newName = ClientNameMother::create('New Name Corp');
        $newSectorId = SectorIdMother::create();
        $client->update($newName, $newSectorId);

        $events = $client->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ClientWasUpdated::class, $events[0]);
    }

    public function test_GivenActiveClient_WhenDeactivate_ThenDeactivatedEventFired(): void
    {
        $client = ClientMother::create();
        $client->pullEvents();

        $client->deactivate();

        $events = $client->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ClientWasDeactivated::class, $events[0]);
        self::assertFalse($client->isActive());
    }

    public function test_GivenInactiveClient_WhenDeactivate_ThenNoEventFired(): void
    {
        $client = ClientMother::create();
        $client->deactivate();
        $client->pullEvents();

        $client->deactivate(); // idempotent

        self::assertCount(0, $client->pullEvents());
    }

    public function test_GivenInactiveClient_WhenActivate_ThenActivatedEventFired(): void
    {
        $client = ClientMother::create();
        $client->deactivate();
        $client->pullEvents();

        $client->activate();

        $events = $client->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ClientWasActivated::class, $events[0]);
        self::assertTrue($client->isActive());
    }

    public function test_GivenActiveClient_WhenActivate_ThenNoEventFired(): void
    {
        $client = ClientMother::create();
        $client->pullEvents();

        $client->activate(); // idempotent

        self::assertCount(0, $client->pullEvents());
    }

    public function test_GivenActiveClient_WhenToggle_ThenDeactivated(): void
    {
        $client = ClientMother::create();
        $client->pullEvents();

        $client->toggleActivation();

        self::assertFalse($client->isActive());
        $events = $client->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ClientWasDeactivated::class, $events[0]);
    }

    public function test_GivenInactiveClient_WhenToggle_ThenActivated(): void
    {
        $client = ClientMother::create();
        $client->deactivate();
        $client->pullEvents();

        $client->toggleActivation();

        self::assertTrue($client->isActive());
        $events = $client->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ClientWasActivated::class, $events[0]);
    }
}
