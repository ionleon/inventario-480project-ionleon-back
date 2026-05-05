<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
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

    public function countContactsForClient(Client $client): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.client = :client')
            ->setParameter('client', $client)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByClient(Client $client): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.client = :client')
            ->setParameter('client', $client);
    }

    //    /**
    //     * @return Contact[] Returns an array of Contact objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contact
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
