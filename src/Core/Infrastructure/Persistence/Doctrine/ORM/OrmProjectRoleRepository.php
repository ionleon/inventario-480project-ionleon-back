<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\ProjectRole\ProjectRoleNotFoundException;
use App\Core\Domain\Model\Aggregate\ProjectRole;
use App\Core\Domain\Model\Repository\ProjectRoleRepository;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmProjectRoleRepository implements ProjectRoleRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(ProjectRole $projectRole): void
    {
        $this->em->persist($projectRole);
    }

    public function remove(ProjectRole $projectRole): void
    {
        $this->em->remove($projectRole);
    }

    public function find(ProjectRoleId $id): ?ProjectRole
    {
        return $this->em->find(ProjectRole::class, $id);
    }

    public function findOneOrFail(ProjectRoleId $id): ProjectRole
    {
        return $this->find($id) ?? throw new ProjectRoleNotFoundException((string) $id);
    }

    public function findOneByName(ProjectRoleName $name): ?ProjectRole
    {
        return $this->em->getRepository(ProjectRole::class)->findOneBy(['name' => $name]);
    }

    /** @return list<ProjectRole> */
    public function all(): array
    {
        /** @var list<ProjectRole> */
        return $this->em->getRepository(ProjectRole::class)->findAll();
    }
}
