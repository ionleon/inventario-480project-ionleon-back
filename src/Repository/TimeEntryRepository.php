<?php

namespace App\Repository;

use App\Entity\AppUser;
use App\Entity\TimeEntry;
use App\ProjectManagement\Domain\Project\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function qbByProjectAndUser(Project $project, ?AppUser $user = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('te')
            ->innerJoin('te.projectUser', 'pu')
            ->andWhere('pu.project = :project')
            ->setParameter('project', $project);

        if ($user) {
            $qb->andWhere('pu.user = :user')
                ->setParameter('user', $user);
        }

        $qb->orderBy('te.date', 'DESC');

        return $qb;
    }

    public function qbByUser(AppUser $user): QueryBuilder
    {
        return $this->createQueryBuilder('te')
            ->innerJoin('te.projectUser', 'pu')
            ->where('pu.appUser = :user')
            ->setParameter('user', $user)
            ->orderBy('te.date', 'DESC');
    }

    public function getTotalHoursByUser(AppUser $user): float
    {
        $qb = $this->createQueryBuilder('te')
            ->select('SUM(te.hour)') // Sumamos la columna 'hour'
            ->innerJoin('te.projectUser', 'pu')
            ->where('pu.appUser = :user')
            ->setParameter('user', $user);


        return (float) $qb->getQuery()->getSingleScalarResult();
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
