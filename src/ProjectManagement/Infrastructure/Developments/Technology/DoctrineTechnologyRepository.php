<?php

namespace App\ProjectManagement\Infrastructure\Developments\Technology;

use App\ProjectManagement\Domain\Developments\Technology\Technology;
use App\ProjectManagement\Domain\Developments\Technology\TechnologyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technology>
 */
class DoctrineTechnologyRepository extends ServiceEntityRepository implements TechnologyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    public function findById(string $id): ?Technology
    {
        return $this->getEntityManager()->find(Technology::class, $id);
    }

    public function save(Technology $technology): void
    {
        $this->getEntityManager()->persist($technology);
        $this->getEntityManager()->flush();
    }

    public function delete(Technology $technology): void
    {
        $this->getEntityManager()->remove($technology);
        $this->getEntityManager()->flush();
    }
}
