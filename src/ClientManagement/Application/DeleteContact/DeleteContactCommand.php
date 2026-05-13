<?php

namespace App\ClientManagement\Application\DeleteContact;

final readonly class DeleteContactCommand
{
    public function __construct(
        public string $contactId,
    ) {}
}
