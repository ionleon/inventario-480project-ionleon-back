<?php

namespace App\Repository;

use App\Entity\AppUser;
use App\Entity\Client;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function qbByFilters(?string $term, ?Client $client, ?bool $isActive) : QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
                    ->innerJoin('p.client', 'c');

        if($term) {
            $qb->andWhere('LOWER(p.name) LIKE LOWER(:term) OR LOWER(p.description) LIKE LOWER(:term) OR LOWER(c.name) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$term.'%');
        }

        if($client) {
            $qb->andWhere('p.client = :client')
                ->setParameter('client', $client);
        }

        if($isActive !== null) {
            $qb->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        $qb->orderBy('p.startDate', 'DESC')
            ->addOrderBy('p.name', 'ASC');

        return $qb;

    }

    public function qbByClient(Client $client): QueryBuilder
    {
         return $this->createQueryBuilder('p')
             ->where('p.client = :client')
             ->setParameter('client', $client);
    }

    public function findByUser(AppUser $user): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.projectUsers', 'pu')
            ->where('pu.appUser = :user')
            ->setParameter('user', $user)
            ->orderBy('p.name', 'ASC');
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
