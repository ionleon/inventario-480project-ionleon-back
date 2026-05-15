<?php

namespace App\TimeManagement\Infrastructure;

use App\ProjectManagement\Domain\Project\Project;
use App\Shared\Domain\Pagination\PaginatedResult;
use App\TimeManagement\Domain\TimeEntry;
use App\TimeManagement\Domain\TimeEntryRepositoryInterface;
use App\UserManagement\Domain\AppUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 */
class DoctrineTimeEntryRepository extends ServiceEntityRepository implements TimeEntryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
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
        return $this->find($id);
    }

    public function findByProjectAndUserPaginated(Project $project, ?AppUser $user, int $page, int $limit): PaginatedResult
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

        return $this->paginateQueryBuilder($qb, $page, $limit);
    }

    public function findByUserPaginated(AppUser $user, int $page, int $limit): PaginatedResult
    {
        $qb = $this->createQueryBuilder('te')
            ->innerJoin('te.projectUser', 'pu')
            ->where('pu.appUser = :userId')
            ->setParameter('userId', $user)
            ->orderBy('te.date', 'DESC');

        return $this->paginateQueryBuilder($qb, $page, $limit);
    }

    public function save(TimeEntry $timeEntry): void
    {
        $this->getEntityManager()->persist($timeEntry);
        $this->getEntityManager()->flush();
    }

    public function delete(TimeEntry $timeEntry): void
    {
        $this->getEntityManager()->remove($timeEntry);
        $this->getEntityManager()->flush();
    }

    private function paginateQueryBuilder(QueryBuilder $qb, int $page, int $limit): PaginatedResult
    {
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $items = iterator_to_array($paginator->getIterator());

        return new PaginatedResult(
            $items,
            $totalItems,
            $page,
            $limit
        );

    }

}
