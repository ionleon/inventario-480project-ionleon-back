<?php

namespace App\ClientManagement\Application\MarkContactAsMain;

final readonly class MarkContactAsMainCommand
{
    public function __construct(
        public string $contactId,
    ) {}
}
