<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Domain\ProjectRole\ProjectRole;
use App\ProjectManagement\Domain\ProjectRole\ProjectRoleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectRole>
 */
class DoctrineProjectRoleRepository extends ServiceEntityRepository implements ProjectRoleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectRole::class);
    }


    public function findById(string $id): ?ProjectRole
    {
        return $this->find($id);
    }

    /**
     * @return ProjectRole[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(ProjectRole $role): void
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }

    public function delete(ProjectRole $role): void
    {
        $this->getEntityManager()->remove($role);
        $this->getEntityManager()->flush();
    }
}
