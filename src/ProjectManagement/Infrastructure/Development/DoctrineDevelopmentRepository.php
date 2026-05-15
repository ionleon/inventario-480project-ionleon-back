<?php

namespace App\ProjectManagement\Infrastructure\Development;

use App\ProjectManagement\Domain\Development\Development;
use App\ProjectManagement\Domain\Development\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Development>
 */
class DoctrineDevelopmentRepository extends ServiceEntityRepository implements DevelopmentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Development::class);
    }

    public function findById(string $id): ?Development
    {
        return $this->find($id);
    }

    public function findByProject(Project $project): array
    {
        return $this->getEntityManager()
                    ->getRepository(Development::class)
                    ->findByProject($project);
    }

    public function save(Development $development): void
    {
        $this->getEntityManager()->persist($development);
        $this->getEntityManager()->flush();
    }

    public function delete(Development $development): void
    {
        $this->getEntityManager()->remove($development);
        $this->getEntityManager()->flush();
    }
}
