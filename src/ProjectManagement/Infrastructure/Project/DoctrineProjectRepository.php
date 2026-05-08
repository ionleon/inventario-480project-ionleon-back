<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\Project\ProjectFilters;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Project>
 */
class DoctrineProjectRepository extends ServiceEntityRepository implements ProjectRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findByFilters(ProjectFilters $filters) : array
    {
        $qb = $this->createFilteredQueryBuilder($filters);

        return $qb->getQuery()
            ->getResult();

    }

    public function findByClient(int $clientId): array
    {
         return $this->createQueryBuilder('p')
             ->where('p.client = :clientId')
             ->setParameter('clientId', $clientId)
             ->getQuery()
             ->getResult();
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.projectUsers', 'pu')
            ->where('pu.appUser = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws Exception
     */
    public function findByFiltersPaginated(ProjectFilters $filters, int $page, int $limit): PaginatedResult
    {
        $qb = $this->createFilteredQueryBuilder($filters);

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

    private function createFilteredQueryBuilder(ProjectFilters $filters) : QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.client', 'c');

        if($filters->term) {
            $qb->andWhere('LOWER(p.name) LIKE LOWER(:term) OR LOWER(p.description) LIKE LOWER(:term) OR LOWER(c.name) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$filters->term.'%');
        }

        if($filters->clientId) {
            $qb->andWhere('p.client = :clientId')
                ->setParameter('clientId', $filters->clientId);
        }

        if($filters->isActive !== null) {
            $qb->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', $filters->isActive);
        }

        $qb->orderBy('p.startDate', 'DESC')
            ->addOrderBy('p.name', 'ASC');

        return $qb;
    }



}
