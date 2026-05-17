<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\ListContactsByClient;

use App\Core\Domain\Model\Aggregate\Contact;

final readonly class ContactResponse
{
    public function __construct(
        public string $id,
        public string $clientId,
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public ?string $note,
        public bool $isMain,
    ) {
    }

    public static function from(Contact $contact): self
    {
        return new self(
            id: (string) $contact->id(),
            clientId: (string) $contact->clientId(),
            fullName: (string) $contact->fullName(),
            email: (string) $contact->email(),
            phoneNumber: (string) $contact->phoneNumber(),
            note: $contact->note(),
            isMain: $contact->isMain(),
        );
    }
}
