<?php

namespace App\Service;

use App\ClientManagement\Domain\Client;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class ContactManager
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly EntityManagerInterface $em
    ) {}

    public function create(array $data, ?Client $client = null): Contact
    {
        if (!isset($data['id'], $data['fullName'])) {
            throw new \InvalidArgumentException('ID and name cannot be empty.');
        }


        if (!$client && isset($data['client_id'])) {
            $client = $this->em->getRepository(Client::class)->find($data['client_id']);
        }

        if (!$client) {
            throw new NotFoundHttpException('Client not found.');
        }

        $contact = new Contact();

        try {
            $contact->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid UUID format.');
        }

        $contact->setClient($client);

        return $this->save($contact, $data);
    }

    public function save(Contact $contact, array $data): Contact
    {
        if (isset($data['fullName'])) {
            $contact->setFullName($data['fullName']);
        }

        if (isset($data['email'])) {
            $contact->setEmail($data['email']);
        }

        if (isset($data['phoneNumber'])) {
            $contact->setPhoneNumber($data['phoneNumber']);
        }

        if (isset($data['note'])) {
            $contact->setNote($data['note']);
        }

        $totalContacts = $this->contactRepository->countContactsForClient($contact->getClient());
        $wantsToBeMain = $data['isMain'] ?? $contact->isMain();

        if ($totalContacts === 0) {
            $contact->setIsMain(true);
        }

        elseif ($totalContacts === 1 && $wantsToBeMain === false) {
            $contact->setIsMain(true);
        }
        else {
            $contact->setIsMain($wantsToBeMain);
        }

        if ($contact->isMain()) {
            $this->contactRepository->resetMainContactsForClient(
                $contact->getClient(),
                $contact
            );
        }

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    public function delete(Contact $contact): void
    {
        $client = $contact->getClient();
        $wasMain = $contact->isMain();

        $totalContacts = $this->contactRepository->countContactsForClient($contact->getClient());

        if ($totalContacts <= 1) {
            throw new \LogicException('Cannot delete contact: clients must have at least one contact.');
        }

        $this->em->remove($contact);
        $this->em->flush();

        if ($wasMain) {
            $remainingContacts = $client->getContacts();

            if (!$remainingContacts->isEmpty()){
                $newMain = $remainingContacts->first();
                $newMain->setIsMain(true);
                $this->em->flush();
            }
        }
    }
}
