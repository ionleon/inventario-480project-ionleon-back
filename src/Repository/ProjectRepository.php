<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findByFilters(?string $term, ?string $clientId, ?bool $isActive)
    {
        $qb = $this->createQueryBuilder('p');

        if($term) {
            $qb->andWhere('p.name LIKE :term OR p.description LIKE :term')
                ->setParameter('term', '%'.$term.'%');
        }

        if($clientId) {
            $qb->andWhere('p.client = :clientId')
                ->setParameter('clientId', $clientId);
        }

        if($isActive !== null) {
            $qb->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        $qb->orderBy('p.startDate', 'DESC')
            ->addOrderBy('p.name', 'ASC');

        return $qb->getQuery()->getResult();

    }

    public function findByClientId(string $clientId): array
    {
     return $this->createQueryBuilder('p')
         ->innerJoin('p.client', 'c')
         ->andWhere('c.id = :clientId')
         ->setParameter('clientId', $clientId)
         ->getQuery()
         ->getResult();
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
