<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Contact;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Service\Contact\CreateContact\CreateContactService;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactIdMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactNameMother;
use PHPUnit\Framework\TestCase;

final class CreateContactServiceTest extends TestCase
{
    public function test_GivenValidArgs_WhenInvoke_ThenContactIsAdded(): void
    {
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willReturn(ClientMother::create());

        $contactRepo = $this->createMock(ContactRepository::class);
        $contactRepo->method('findByClient')->willReturn([]);
        $contactRepo->expects(self::once())->method('add');

        $service = new CreateContactService($clientRepo, $contactRepo);
        $service(
            ContactIdMother::create(),
            ClientIdMother::create(),
            ContactNameMother::create(),
            new Email('test@example.com'),
            new Phone('+34600000000'),
            null,
        );
    }

    public function test_GivenClientNotFound_WhenInvoke_ThenThrowsClientNotFoundException(): void
    {
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willThrowException(new ClientNotFoundException());

        $contactRepo = $this->createMock(ContactRepository::class);
        $contactRepo->expects(self::never())->method('add');

        $this->expectException(ClientNotFoundException::class);

        (new CreateContactService($clientRepo, $contactRepo))(
            ContactIdMother::create(),
            ClientIdMother::create(),
            ContactNameMother::create(),
            new Email('test@example.com'),
            new Phone('+34600000000'),
            null,
        );
    }

    public function test_GivenFirstContactForClient_WhenInvoke_ThenIsMain(): void
    {
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willReturn(ClientMother::create());

        $contactRepo = $this->createMock(ContactRepository::class);
        $contactRepo->method('findByClient')->willReturn([]);
        $contactRepo->method('add');

        $service = new CreateContactService($clientRepo, $contactRepo);
        $contact = $service(
            ContactIdMother::create(),
            ClientIdMother::create(),
            ContactNameMother::create(),
            new Email('test@example.com'),
            new Phone('+34600000000'),
            null,
        );

        self::assertTrue($contact->isMain());
    }
}
