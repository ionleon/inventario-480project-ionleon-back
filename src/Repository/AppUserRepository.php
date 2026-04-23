<?php

namespace App\Repository;

use App\Entity\AppUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppUser>
 */
class AppUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUser::class);
    }

    public function findByFilters(?string $term, ?string $role, ?bool $isActive)
    {
        $qb = $this->createQueryBuilder('u');

        if($term) {
            $qb->andWhere('u.name LIKE :term OR u.surname LIKE :term OR u.email LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        if($role) {
            $qb->andWhere('u.role LIKE :role')
                ->setParameter('role', '%'. $role .'%');
        }

        if($isActive !== null) {
            $qb->andWhere('u.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        $qb->orderBy('u.surname' , 'ASC')
            ->addOrderBy('u.name' , 'ASC');

        return $qb->getQuery()->getResult();
    }

    #Revisar esto para mas adelante
    public function findUserByProject(string $projectId): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('App\Entity\ProjectUser', 'pu', 'WITH', 'pu.appUser = u')
            ->innerJoin('pu.project', 'p')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return AppUser[] Returns an array of AppUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AppUser
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
