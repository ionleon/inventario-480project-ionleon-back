<?php

namespace App\ClientManagement\Infrastructure\Contact;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Contact\Contact;
use App\ClientManagement\Domain\Contact\ContactRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class DoctrineContactRepository extends ServiceEntityRepository implements ContactRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function findById(string $id): ?Contact
    {
        return $this->find($id);
    }

    public function findAll() : array
    {
        return parent::findAll();
    }

    public function save(Contact $contact): void
    {
        $this->getEntityManager()->persist($contact);
        $this->getEntityManager()->flush();
    }

    public function remove(Contact $contact): void
    {
        $this->getEntityManager()->remove($contact);
        $this->getEntityManager()->flush();
    }

    public function resetMainContactsForClient(Client $client, ?Contact $excludeContact = null): void
    {
        $qb = $this->createQueryBuilder('c')
            ->update()
            ->set('c.isMain', ':false')
            ->where('c.client = :client')
            ->andWhere('c.isMain = :true')
            ->setParameter('false', false)
            ->setParameter('true', true)
            ->setParameter('client', $client);

        if ($excludeContact && $excludeContact->getId()) {
            $qb->andWhere('c.id != :excludeId')
                ->setParameter('excludeId', $excludeContact->getId());
        }

        $qb->getQuery()->execute();
    }

    public function countContactsForClient(string $clientId): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.client = :client')
            ->setParameter('client', $clientId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByClient(Client $client): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.client = :client')
            ->setParameter('client', $client);
    }


    public function findByClientPaginated(string $clientId, int $page, int $limit): PaginatedResult
    {
        return $this->createQueryBuilder('c')
            ->where('c.client = :client')
            ->setParameter('client', $client);
    }
}
