<?php

namespace App\ClientManagement\Application\CreateContact;

use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Contact\Contact;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateContactHandler
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ContactRepositoryInterface $contactRepository,
    ) {}

    public function handle(CreateContactCommand $command): Contact
    {
        $client = $this->clientRepository->findById($command->clientId);

        if (!$client) {
            throw new \DomainException('Client not found');
        }

        $contact = new Contact();
        $contact->setId(Uuid::fromString($command->id));
        $contact->setClient($client);
        $contact->setFullName($command->fullName);
        $contact->setEmail($command->email);
        $contact->setPhoneNumber($command->phoneNumber);
        $contact->setNote($command->note);

        $totalContacts = $this->contactRepository->countContactsForClient($client);
        $contact->setIsMain($totalContacts === 0 || $command->isMain === true);

        if ($contact->isMain()) {
            $this->contactRepository->resetMainContactsForClient($client, $contact);
        }

        $this->contactRepository->save($contact);

        return $contact;
    }
}
