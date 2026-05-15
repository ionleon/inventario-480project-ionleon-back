<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Contact;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Service\Contact\UpdateContact\UpdateContactService;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactIdMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactNameMother;
use PHPUnit\Framework\TestCase;

final class UpdateContactServiceTest extends TestCase
{
    public function test_GivenExistingContact_WhenInvoke_ThenContactUpdated(): void
    {
        $contact = ContactMother::create();

        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willReturn($contact);

        $service = new UpdateContactService($repo);
        $updatedContact = $service(
            $contact->id(),
            ContactNameMother::create('Updated Name'),
            new Email('updated@example.com'),
            new Phone('+34699999999'),
            'Updated note',
        );

        self::assertSame('Updated Name', (string) $updatedContact->fullName());
        self::assertSame('updated@example.com', (string) $updatedContact->email());
    }

    public function test_GivenContactNotFound_WhenInvoke_ThenThrowsContactNotFoundException(): void
    {
        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new ContactNotFoundException());

        $this->expectException(ContactNotFoundException::class);

        (new UpdateContactService($repo))(
            ContactIdMother::create(),
            ContactNameMother::create(),
            new Email('test@example.com'),
            new Phone('+34600000000'),
            null,
        );
    }
}
