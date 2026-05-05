<?php

namespace App\Repository;

use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\ProjectUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectUser>
 */
class ProjectUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectUser::class);
    }

    public function qbAllByProjects(Project $project): QueryBuilder
    {
        return $this->createQueryBuilder('pu')
            ->innerJoin('pu.appUser', 'u')->addSelect('u')
            ->innerJoin('pu.projectRole', 'r')->addSelect('r')
            ->where('pu.project = :project')
            ->setParameter('project', $project);
    }


    public function findOneByProjectAndUser(Project $project, AppUser $user): array
    {
        return $this->createQueryBuilder('pu')
            ->innerJoin('pu.appUser', 'u')->addSelect('u')
            ->innerJoin('pu.projectRole', 'r')->addSelect('r')
            ->where('pu.project = :project')
            ->andWhere('pu.appUser = :user')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return ProjectUser[] Returns an array of ProjectUser objects
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

    //    public function findOneBySomeField($value): ?ProjectUser
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
