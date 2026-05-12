<?php

namespace App\TimeManagement\Infrastructure;

use App\ProjectManagement\Domain\Project\Project;
use App\Shared\Domain\Pagination\PaginatedResult;
use App\TimeManagement\Domain\TimeEntry;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;
use App\UserManagement\Domain\AppUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 */
class TimeEntryRepository extends ServiceEntityRepository implements TimeEntryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
    }

    public function qbByProjectAndUser(Project $project, ?AppUser $user = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('te')
            ->innerJoin('te.projectUser', 'pu')
            ->andWhere('pu.project = :projectd')
            ->setParameter('projectd', $project);

        if ($user) {
            $qb->andWhere('pu.user = :userId')
                ->setParameter('userId', $user);
        }

        $qb->orderBy('te.date', 'DESC');

        return $qb;
    }

    public function qbByUser(AppUser $user): QueryBuilder
    {
        return $this->createQueryBuilder('te')
            ->innerJoin('te.projectUser', 'pu')
            ->where('pu.appUser = :userId')
            ->setParameter('userId', $user)
            ->orderBy('te.date', 'DESC');
    }

    public function getTotalHoursByUser(AppUser $user): float
    {
        $qb = $this->createQueryBuilder('te')
            ->select('SUM(te.hour)') // Sumamos la columna 'hour'
            ->innerJoin('te.projectUser', 'pu')
            ->where('pu.appUser = :userId')
            ->setParameter('userId', $user);


        return (float) $qb->getQuery()->getSingleScalarResult();
    }


    public function findById(string $id): ?TimeEntry
    {
        // TODO: Implement findById() method.
    }

    public function findByProjectAndUserPaginated(Project $project, ?AppUser $user, int $page, int $limit): PaginatedResult
    {
        // TODO: Implement findByProjectAndUserPaginated() method.
    }

    public function findByUserPaginated(AppUser $user, int $page, int $limit): PaginatedResult
    {
        // TODO: Implement findByUserPaginated() method.
    }

    public function save(TimeEntry $timeEntry): void
    {
        // TODO: Implement save() method.
    }

    public function remove(TimeEntry $timeEntry): void
    {
        // TODO: Implement remove() method.
    }
}
