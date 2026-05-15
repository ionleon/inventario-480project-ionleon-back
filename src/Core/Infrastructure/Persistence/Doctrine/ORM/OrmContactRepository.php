<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\Contact\ContactNotFoundException;
use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\Repository\ContactRepository;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Contact\ContactId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmContactRepository implements ContactRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(Contact $contact): void
    {
        $this->em->persist($contact);
    }

    public function remove(Contact $contact): void
    {
        $this->em->remove($contact);
    }

    public function find(ContactId $id): ?Contact
    {
        return $this->em->find(Contact::class, $id);
    }

    public function findOneOrFail(ContactId $id): Contact
    {
        return $this->find($id) ?? throw new ContactNotFoundException((string) $id);
    }

    /** @return list<Contact> */
    public function findByClient(ClientId $clientId): array
    {
        /** @var list<Contact> */
        return $this->em->getRepository(Contact::class)->findBy(['clientId' => $clientId]);
    }

    public function findMainByClient(ClientId $clientId): ?Contact
    {
        return $this->em->getRepository(Contact::class)->findOneBy([
            'clientId' => $clientId,
            'isMain' => true,
        ]);
    }

    /** @return list<Contact> */
    public function all(): array
    {
        /** @var list<Contact> */
        return $this->em->getRepository(Contact::class)->findAll();
    }
}
