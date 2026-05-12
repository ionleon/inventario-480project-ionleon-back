<?php

namespace App\ClientManagement\Infrastructure\Client;

use App\ClientManagement\Domain\Client\Client;
use App\ClientManagement\Domain\Client\ClientFilters;
use App\ClientManagement\Domain\Client\ClientRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class DoctrineClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findWithSectorsPaginated(ClientFilters $filters, int $page, int $limit): PaginatedResult
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

    private function createFilteredQueryBuilder(ClientFilters $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('s')
            ->leftJoin('c.sector', 's');

        if($filters->term) {
            $qb->andWhere('LOWER(c.name) LIKE LOWER(:term) OR LOWER(s.name) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $filters->term .'%');
        }

        if ($filters->isActive !== null) {
            $qb->andWhere('c.isActive = :isActive')
                ->setParameter('isActive', $filters->isActive);
        }

        return $qb->orderBy('c.name' , 'ASC');
    }

    public function findById(string $id): ?Client
    {
        return $this->find($id);
    }

    public function save(Client $client): void
    {
        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();
    }

    public function delete(Client $client): void
    {
        $this->getEntityManager()->remove($client);
        $this->getEntityManager()->flush();
    }
}
