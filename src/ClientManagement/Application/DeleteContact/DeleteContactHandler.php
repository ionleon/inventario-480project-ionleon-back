<?php

namespace App\ClientManagement\Application\DeleteContact;

use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;

final class DeleteContactHandler
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
    ) {}

    public function handle(DeleteContactCommand $command): void
    {
        $contact = $this->contactRepository->findById($command->contactId);

        if (!$contact) {
            throw new \DomainException('Contact not found');
        }

        $client = $contact->getClient();
        $totalContacts = $this->contactRepository->countContactsForClient($client);

        if ($totalContacts <= 1) {
            throw new \LogicException('Cannot delete contact: clients must have at least one contact.');
        }

        $wasMain = $contact->isMain();
        $this->contactRepository->remove($contact);

        if ($wasMain) {
            $remainingContacts = $this->contactRepository->findByClient($client);

            if ($remainingContacts !== []) {
                $newMain = $remainingContacts[0];
                $newMain->setIsMain(true);
                $this->contactRepository->save($newMain);
            }
        }
    }
}
