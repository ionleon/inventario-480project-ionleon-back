<?php

namespace App\ClientManagement\Application\CreateContact;

final readonly class CreateContactCommand
{
    public function __construct(
        public string $clientId,
        public string $id,
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public ?string $note,
        public ?bool $isMain,
    ) {}
}
