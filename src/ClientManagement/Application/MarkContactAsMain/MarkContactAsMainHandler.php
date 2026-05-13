<?php

namespace App\ClientManagement\Application\MarkContactAsMain;

use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;

final class MarkContactAsMainHandler
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
    ) {}

    public function handle(MarkContactAsMainCommand $command): void
    {
        $contact = $this->contactRepository->findById($command->contactId);

        if (!$contact) {
            throw new \DomainException('Contact not found');
        }

        if ($contact->isMain()) {
            return;
        }

        $contact->setIsMain(true);

        $this->contactRepository->resetMainContactsForClient(
            $contact->getClient(),
            $contact
        );

        $this->contactRepository->save($contact);
    }
}
