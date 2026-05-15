<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Contact\ContactId;

interface ContactRepository
{
    public function add(Contact $contact): void;

    public function remove(Contact $contact): void;

    public function find(ContactId $id): ?Contact;

    /** @throws ContactNotFoundException */
    public function findOneOrFail(ContactId $id): Contact;

    /** @return list<Contact> */
    public function findByClient(ClientId $clientId): array;

    public function findMainByClient(ClientId $clientId): ?Contact;

    /** @return list<Contact> */
    public function all(): array;
}
