<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\TimeEntry\TimeEntryNotFoundException;
use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Aggregate\TimeEntry;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\ProjectUser\ProjectUserId;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmTimeEntryRepository implements TimeEntryRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(TimeEntry $timeEntry): void
    {
        $this->em->persist($timeEntry);
    }

    public function remove(TimeEntry $timeEntry): void
    {
        $this->em->remove($timeEntry);
    }

    public function find(TimeEntryId $id): ?TimeEntry
    {
        return $this->em->find(TimeEntry::class, $id);
    }

    public function findOneOrFail(TimeEntryId $id): TimeEntry
    {
        return $this->find($id) ?? throw new TimeEntryNotFoundException((string) $id);
    }

    /** @return list<TimeEntry> */
    public function findByProjectUser(ProjectUserId $projectUserId): array
    {
        /** @var list<TimeEntry> */
        return $this->em->getRepository(TimeEntry::class)->findBy(
            ['projectUserId' => $projectUserId],
            ['date' => 'DESC'],
        );
    }

    /** @return list<TimeEntry> */
    public function findByUser(UserId $userId): array
    {
        // Find all projectUser IDs for this user, then fetch entries
        $projectUsers = $this->em->getRepository(ProjectUser::class)->findBy(['userId' => $userId]);
        $projectUserIds = array_map(static fn(ProjectUser $pu) => $pu->id(), $projectUsers);

        if (empty($projectUserIds)) {
            return [];
        }

        /** @var list<TimeEntry> */
        return $this->em->getRepository(TimeEntry::class)->findBy(
            ['projectUserId' => $projectUserIds],
            ['date' => 'DESC'],
        );
    }

    /** @return list<TimeEntry> */
    public function findByProject(ProjectId $projectId): array
    {
        // Find all projectUser IDs for this project, then fetch entries
        $projectUsers = $this->em->getRepository(ProjectUser::class)->findBy(['projectId' => $projectId]);
        $projectUserIds = array_map(static fn(ProjectUser $pu) => $pu->id(), $projectUsers);

        if (empty($projectUserIds)) {
            return [];
        }

        /** @var list<TimeEntry> */
        return $this->em->getRepository(TimeEntry::class)->findBy(
            ['projectUserId' => $projectUserIds],
            ['date' => 'DESC'],
        );
    }

    public function findOwnerUserId(TimeEntryId $id): UserId
    {
        $row = $this->em->createQueryBuilder()
            ->select('IDENTITY(pu.userId) AS user_id')
            ->from(TimeEntry::class, 'te')
            ->join(ProjectUser::class, 'pu', 'WITH', 'pu.id = te.projectUserId')
            ->where('te.id = :id')
            ->setParameter('id', (string) $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $row || !isset($row['user_id'])) {
            throw new TimeEntryNotFoundException((string) $id);
        }

        return new UserId((string) $row['user_id']);
    }
}
