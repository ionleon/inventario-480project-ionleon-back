<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Event\Contact;

use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Contact\ContactId;
use DateTimeImmutable;

final readonly class ContactWasCreated
{
    public function __construct(
        public ContactId $id,
        public ClientId $clientId,
        public DateTimeImmutable $occurredAt,
    ) {
    }

    public static function from(\App\Core\Domain\Model\Aggregate\Contact $contact): self
    {
        return new self(
            id: $contact->id(),
            clientId: $contact->clientId(),
            occurredAt: new DateTimeImmutable(),
        );
    }
}
