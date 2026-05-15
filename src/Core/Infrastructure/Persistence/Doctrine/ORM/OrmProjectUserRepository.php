<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\ProjectUser\ProjectUserNotFoundException;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\User\UserId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmProjectUserRepository implements ProjectUserRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(ProjectUser $projectUser): void
    {
        $this->em->persist($projectUser);
    }

    public function remove(ProjectUser $projectUser): void
    {
        $this->em->remove($projectUser);
    }

    public function find(ProjectUserId $id): ?ProjectUser
    {
        return $this->em->find(ProjectUser::class, $id);
    }

    public function findOneOrFail(ProjectUserId $id): ProjectUser
    {
        return $this->find($id) ?? throw new ProjectUserNotFoundException((string) $id);
    }

    /** @return list<ProjectUser> */
    public function findByProject(ProjectId $projectId): array
    {
        /** @var list<ProjectUser> */
        return $this->em->createQueryBuilder()
            ->select('pu')
            ->from(ProjectUser::class, 'pu')
            ->where('CAST(pu.projectId AS string) = :projectId')
            ->setParameter('projectId', (string) $projectId)
            ->orderBy('pu.isActive', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<ProjectUser> */
    public function findActiveByUser(UserId $userId): array
    {
        /** @var list<ProjectUser> */
        return $this->em->createQueryBuilder()
            ->select('pu')
            ->from(ProjectUser::class, 'pu')
            ->where('CAST(pu.userId AS string) = :userId')
            ->andWhere('pu.isActive = :isActive')
            ->setParameter('userId', (string) $userId)
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();
    }

    public function findOneByProjectAndUser(ProjectId $projectId, UserId $userId): ?ProjectUser
    {
        /** @var ?ProjectUser */
        return $this->em->createQueryBuilder()
            ->select('pu')
            ->from(ProjectUser::class, 'pu')
            ->where('CAST(pu.projectId AS string) = :projectId')
            ->andWhere('CAST(pu.userId AS string) = :userId')
            ->setParameter('projectId', (string) $projectId)
            ->setParameter('userId', (string) $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
