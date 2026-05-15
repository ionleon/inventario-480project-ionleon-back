<?php

namespace App\ClientManagement\Application\UpdateContact;

use App\ClientManagement\Domain\Contact\Contact;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;

final class UpdateContactHandler
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
    ) {}

    public function handle(UpdateContactCommand $command): Contact
    {
        $contact = $this->contactRepository->findById($command->contactId);

        if (!$contact) {
            throw new \DomainException('Contact not found');
        }

        if ($command->fullName !== null) {
            $contact->setFullName($command->fullName);
        }

        if ($command->email !== null) {
            $contact->setEmail($command->email);
        }

        if ($command->phoneNumber !== null) {
            $contact->setPhoneNumber($command->phoneNumber);
        }

        if ($command->note !== null) {
            $contact->setNote($command->note);
        }

        if ($command->isMain !== null) {
            $totalContacts = $this->contactRepository->countContactsForClient($contact->getClient());

            if ($totalContacts === 1 && $command->isMain === false) {
                $contact->setIsMain(true);
            } else {
                $contact->setIsMain($command->isMain);
            }
        }

        if ($contact->isMain()) {
            $this->contactRepository->resetMainContactsForClient(
                $contact->getClient(),
                $contact
            );
        }

        $this->contactRepository->save($contact);

        return $contact;
    }
}
