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
        return $this->em->getRepository(ProjectUser::class)->findBy(
            ['projectId' => $projectId],
            ['isActive' => 'DESC'],
        );
    }

    /** @return list<ProjectUser> */
    public function findActiveByUser(UserId $userId): array
    {
        /** @var list<ProjectUser> */
        return $this->em->getRepository(ProjectUser::class)->findBy([
            'userId' => $userId,
            'isActive' => true,
        ]);
    }

    public function findOneByProjectAndUser(ProjectId $projectId, UserId $userId): ?ProjectUser
    {
        /** @var ?ProjectUser */
        return $this->em->getRepository(ProjectUser::class)->findOneBy([
            'projectId' => $projectId,
            'userId' => $userId,
        ]);
    }
}
