<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Contact;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Service\Contact\DeleteContact\DeleteContactService;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactIdMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactMother;
use PHPUnit\Framework\TestCase;

final class DeleteContactServiceTest extends TestCase
{
    public function test_GivenExistingContact_WhenInvoke_ThenContactRemoved(): void
    {
        $contact = ContactMother::create();

        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willReturn($contact);
        $repo->expects(self::once())->method('remove');

        $service = new DeleteContactService($repo);
        $service($contact->id());
    }

    public function test_GivenContactNotFound_WhenInvoke_ThenThrowsContactNotFoundException(): void
    {
        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new ContactNotFoundException());

        $this->expectException(ContactNotFoundException::class);

        (new DeleteContactService($repo))(ContactIdMother::create());
    }
}
