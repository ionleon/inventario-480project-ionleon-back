<?php

namespace App\ClientManagement\Application\UpdateContact;

final readonly class UpdateContactCommand
{
    public function __construct(
        public string $contactId,
        public ?string $fullName,
        public ?string $email,
        public ?string $phoneNumber,
        public ?string $note,
        public ?bool $isMain,
    ) {}
}
