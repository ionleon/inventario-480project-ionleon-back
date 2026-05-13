<?php

namespace App\ClientManagement\Application\GetClient;

final readonly class GetClientQuery
{
    public function __construct(
        public string $clientId,
    ) {}
}
