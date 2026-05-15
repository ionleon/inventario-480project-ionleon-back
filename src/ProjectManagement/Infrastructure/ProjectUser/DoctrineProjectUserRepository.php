<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectUser>
 */
class DoctrineProjectUserRepository extends ServiceEntityRepository implements ProjectUserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectUser::class);
    }

    public function findById(string $id): ?ProjectUser
    {
        return $this->find($id);
    }


    public function findOneByProjectAndUser(string $projectId, string $userId): ?ProjectUser
    {
        return $this->createQueryBuilder('pu')
            ->innerJoin('pu.appUser', 'u')->addSelect('u')
            ->innerJoin('pu.projectRole', 'r')->addSelect('r')
            ->where('pu.project = :projectId')
            ->andWhere('pu.appUser = :userId')
            ->setParameter('projectId', $projectId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws \Exception
     */
    public function findByProjectPaginated(string $projectId, int $page, int $limit): PaginatedResult
    {
        $qb = $this->createQueryBuilder('pu')
            ->where('pu.project = :projectId')
            ->setParameter('projectId', $projectId);


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

    public function save(ProjectUser $assignment, bool $flush = true): void
    {
        $this->getEntityManager()->persist($assignment);
        if ($flush) $this->getEntityManager()->flush();
    }

    public function remove(ProjectUser $assignment): void
    {
        $this->getEntityManager()->remove($assignment);
        $this->getEntityManager()->flush();
    }

    public function transaction(callable $operation): void
    {
       $this->getEntityManager()->wrapInTransaction($operation);
    }


    public function updateActivationByUserId(string $userId, bool $isActive): void
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->update(ProjectUser::class, 'pu')
            ->set('pu.isActive', ':status')
            ->where('pu.appUser = :userId')
            ->setParameter('status', $isActive)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
}
