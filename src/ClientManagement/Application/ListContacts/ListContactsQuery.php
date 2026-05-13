<?php

namespace App\ClientManagement\Application\ListContacts;

final readonly class ListContactsQuery
{
    public function __construct(
        public string $clientId,
        public int $page,
        public int $limit,
    ) {}
}
