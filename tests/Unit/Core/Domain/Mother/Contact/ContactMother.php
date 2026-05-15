<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Contact;

use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;

final class ContactMother
{
    public static function create(
        ?ContactId $id = null,
        ?ClientId $clientId = null,
        ?ContactName $fullName = null,
        ?Email $email = null,
        ?Phone $phoneNumber = null,
        ?string $note = null,
        bool $isMain = false,
    ): Contact {
        return Contact::create(
            id: $id ?? ContactIdMother::create(),
            clientId: $clientId ?? ClientIdMother::create(),
            fullName: $fullName ?? ContactNameMother::create(),
            email: $email ?? new Email('john.doe@example.com'),
            phoneNumber: $phoneNumber ?? new Phone('+34600000000'),
            note: $note,
            isMain: $isMain,
        );
    }
}
