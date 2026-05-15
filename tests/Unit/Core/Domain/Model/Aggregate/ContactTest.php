<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Event\Contact\ContactWasCreated;
use App\Core\Domain\Model\Event\Contact\ContactWasDeleted;
use App\Core\Domain\Model\Event\Contact\ContactWasMarkedAsMain;
use App\Core\Domain\Model\Event\Contact\ContactWasUpdated;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactName;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactIdMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactNameMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use PHPUnit\Framework\TestCase;

final class ContactTest extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenInstanceWithCreatedEvent(): void
    {
        $contact = ContactMother::create();
        $events = $contact->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(ContactWasCreated::class, $events[0]);
    }

    public function test_GivenContact_WhenCreate_ThenNotMain(): void
    {
        $contact = ContactMother::create(isMain: false);
        self::assertFalse($contact->isMain());
    }

    public function test_GivenContact_WhenGetters_ThenReturnExpectedValues(): void
    {
        $id = ContactIdMother::create('00000000-0000-4000-8000-000000000001');
        $clientId = ClientIdMother::create('00000000-0000-4000-8000-000000000002');
        $name = ContactNameMother::create('Jane Doe');
        $email = new Email('jane@example.com');
        $phone = new Phone('+34611111111');

        $contact = \App\Core\Domain\Model\Aggregate\Contact::create(
            id: $id,
            clientId: $clientId,
            fullName: $name,
            email: $email,
            phoneNumber: $phone,
            note: 'Test note',
            isMain: true,
        );

        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $contact->id());
        self::assertSame('00000000-0000-4000-8000-000000000002', (string) $contact->clientId());
        self::assertSame('Jane Doe', (string) $contact->fullName());
        self::assertSame('jane@example.com', (string) $contact->email());
        self::assertSame('+34611111111', (string) $contact->phoneNumber());
        self::assertSame('Test note', $contact->note());
        self::assertTrue($contact->isMain());
    }

    public function test_GivenContact_WhenUpdate_ThenUpdatedEventFired(): void
    {
        $contact = ContactMother::create();
        $contact->pullEvents();

        $contact->update(
            ContactNameMother::create('Updated Name'),
            new Email('updated@example.com'),
            new Phone('+34699999999'),
            'Updated note',
        );

        $events = $contact->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ContactWasUpdated::class, $events[0]);
    }

    public function test_GivenNonMainContact_WhenMarkAsMain_ThenMarkedAsMainEventFired(): void
    {
        $contact = ContactMother::create(isMain: false);
        $contact->pullEvents();

        $contact->markAsMain();

        $events = $contact->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ContactWasMarkedAsMain::class, $events[0]);
        self::assertTrue($contact->isMain());
    }

    public function test_GivenMainContact_WhenMarkAsMain_ThenNoEventFired(): void
    {
        $contact = ContactMother::create(isMain: true);
        $contact->pullEvents();

        $contact->markAsMain(); // idempotent

        self::assertCount(0, $contact->pullEvents());
    }

    public function test_GivenMainContact_WhenUnmarkAsMain_ThenNotMain(): void
    {
        $contact = ContactMother::create(isMain: true);
        $contact->pullEvents();

        $contact->unmarkAsMain();

        self::assertFalse($contact->isMain());
        self::assertCount(0, $contact->pullEvents());
    }

    public function test_GivenContact_WhenDelete_ThenDeletedEventFired(): void
    {
        $contact = ContactMother::create();
        $contact->pullEvents();

        $contact->delete();

        $events = $contact->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ContactWasDeleted::class, $events[0]);
    }
}
