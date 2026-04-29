<?php

namespace App\Repository;

use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\TimeEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 */
class TimeEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
    }

    public function findByProjectAndUser(Project $project, ?AppUser $user = null): array
    {
        $qb = $this->createQueryBuilder('te')
            ->innerJoin('te.projectUser', 'pu')
            ->andWhere('pu.project = :project')
            ->setParameter('project', $project);

        if ($user) {
            $qb->andWhere('pu.user = :user')
                ->setParameter('user', $user);
        }

        return $qb->orderBy('te.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return TimeEntry[] Returns an array of TimeEntry objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TimeEntry
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
