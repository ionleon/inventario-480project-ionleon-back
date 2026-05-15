<?php

namespace App\ClientManagement\Application\DeleteClient;

final readonly class DeleteClientCommand
{
    public function __construct(
        public string $clientId,
    ) {}
}
