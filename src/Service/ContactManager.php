<?php

namespace App\Service;

use App\Entity\Client;
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

//    public function save(Contact $contact, array $data): Contact
//    {
//        $contact->setFullName($data['fullName'] ?? $contact->getFullName());
//        $contact->setEmail($data['email'] ?? $contact->getEmail());
//        $contact->setPhoneNumber($data['phoneNumber'] ?? $contact->getPhoneNumber());
//        $contact->setNote($data['note'] ?? $contact->getNote());
//        $contact->setIsActive($data['isActive'] ?? $contact->isActive() ?? true);
//
//        $isMain = $data['isMain'] ?? $contact->isMain() ?? false;
//        $contact->setIsMain($isMain);
//
//        if($contact->isMain()) {
//            $this->contactRepository->resetMainContactsForClient(
//                $contact->getClient(),
//                $contact
//            );
//        }
//
//        $this->em->persist($contact);
//        $this->em->flush();
//
//        $this->em->refresh($contact);
//
//        return $contact;
//    }

    public function create(array $data): Contact
    {
        if (!isset($data['id'], $data['client_id'], $data['fullName'])) {
            throw new \InvalidArgumentException('El cliente y el nombre completo son obligatorios.');
        }

        $client = $this->em->getRepository(Client::class)->find($data['client_id']);
        if (!$client) {
            throw new NotFoundHttpException('Cliente no encontrado.');
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
            return;
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
