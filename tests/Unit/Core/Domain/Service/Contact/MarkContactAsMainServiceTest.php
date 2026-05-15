<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Contact;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Service\Contact\MarkContactAsMain\MarkContactAsMainService;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactIdMother;
use App\Tests\Unit\Core\Domain\Mother\Contact\ContactMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use PHPUnit\Framework\TestCase;

final class MarkContactAsMainServiceTest extends TestCase
{
    public function test_GivenNonMainContact_WhenInvoke_ThenCurrentMainUnmarkedAndNewMarked(): void
    {
        $clientId = ClientIdMother::create();
        $currentMain = ContactMother::create(clientId: $clientId, isMain: true);
        $currentMain->pullEvents();

        $newMain = ContactMother::create(clientId: $clientId, isMain: false);
        $newMain->pullEvents();

        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willReturn($newMain);
        $repo->method('findMainByClient')->willReturn($currentMain);

        $service = new MarkContactAsMainService($repo);
        $service($newMain->id());

        self::assertFalse($currentMain->isMain());
        self::assertTrue($newMain->isMain());
    }

    public function test_GivenAlreadyMainContact_WhenInvoke_ThenNoChange(): void
    {
        $contact = ContactMother::create(isMain: true);
        $contact->pullEvents();

        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willReturn($contact);

        $service = new MarkContactAsMainService($repo);
        $service($contact->id());

        self::assertCount(0, $contact->pullEvents());
    }

    public function test_GivenContactNotFound_WhenInvoke_ThenThrowsContactNotFoundException(): void
    {
        $repo = $this->createMock(ContactRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new ContactNotFoundException());

        $this->expectException(ContactNotFoundException::class);

        (new MarkContactAsMainService($repo))(ContactIdMother::create());
    }
}
