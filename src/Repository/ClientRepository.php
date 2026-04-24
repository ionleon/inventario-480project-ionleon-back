<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findWithSectorsByFilters(?string $term,?bool $isActive  ): array
    {
        $qb = $this->createQueryBuilder('c')
                    ->addSelect('s')
                    ->leftJoin('c.sector', 's');

        if($term) {
            $qb->andWhere('LOWER(c.name) LIKE LOWER(:term) OR LOWER(s.name) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $term .'%');
        }

        if ($isActive !== null) {
            $qb->andWhere('c.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        $qb->orderBy('c.name' , 'ASC');

        return $qb->getQuery()->getResult();

    }

//    /**
//     * @return Client[] Returns an array of Client objects
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

//    public function findOneBySomeField($value): ?Client
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
