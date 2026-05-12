<?php

namespace App\ClientManagement\Application\Contact;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\ClientManagement\Domain\Contact\Contact;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;
use App\ClientManagement\Infrastructure\Contact\DoctrineContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

readonly class ContactService
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    /**
     * @throws \Exception
     */
    public function create(array $data, ?Client $client = null): Contact
    {
        if (!isset($data['id'], $data['fullName'])) {
            throw new \InvalidArgumentException('ID and name cannot be empty.');
        }

        if (!$client && isset($data['client_id'])) {
            $client = $this->clientRepository->findById($data['client_id']);
        }

        if (!$client) {
            throw new \Exception('Client not found.');
        }

        $contact = new Contact();

        try {
            $contact->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid UUID format.', $e->getCode(), $e);
        }

        $contact->setClient($client);

        return $this->update($contact, $data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        $contact->setFullName($data['fullName'] ?? $contact->getFullName());
        $contact->setEmail($data['email'] ?? $contact->getEmail());
        $contact->setPhoneNumber($data['phoneNumber'] ?? $contact->getPhoneNumber());
        $contact->setNote($data['note'] ?? $contact->getNote());

        $totalContacts = $this->contactRepository->countContactsForClient($contact->getClient());
        $wantsToBeMain = $data['is_main'] ?? $contact->isMain();

        if ($totalContacts === 0 || ($totalContacts === 1 && !$wantsToBeMain)) {
            $contact->setIsMain(true);
        } else {
            $contact->setIsMain($wantsToBeMain);
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

    public function delete(Contact $contact): void
    {
        $client = $contact->getClient();
        $totalContacts = $this->contactRepository->countContactsForClient($contact->getClient());

        if ($totalContacts <= 1) {
            throw new \LogicException('Cannot delete contact: clients must have at least one contact.');
        }

        $wasMain = $contact->isMain();
        $this->contactRepository->remove($contact);


        if ($wasMain) {
            $remainingContacts = $this->contactRepository->findByClient($client);

            if (!$remainingContacts->isEmpty()){
                $newMain = $remainingContacts->first();
                $newMain->setIsMain(true);
                $this->contactRepository->save($contact);
            }
        }
    }

    public function markAsMain(Contact $contact): void
    {
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
