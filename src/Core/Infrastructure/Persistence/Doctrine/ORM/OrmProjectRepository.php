<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\Project\ProjectNotFoundException;
use App\Core\Domain\Model\Aggregate\Project;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Model\VO\Project\ProjectName;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final readonly class OrmProjectRepository implements ProjectRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }

    public function find(ProjectId $id): ?Project
    {
        return $this->em->find(Project::class, $id);
    }

    public function findOneOrFail(ProjectId $id): Project
    {
        return $this->find($id) ?? throw new ProjectNotFoundException((string) $id);
    }

    public function findOneByName(ProjectName $name): ?Project
    {
        return $this->em->getRepository(Project::class)->findOneBy(['name' => $name]);
    }

    /** @return list<Project> */
    public function all(): array
    {
        /** @var list<Project> */
        return $this->em->getRepository(Project::class)->findAll();
    }

    public function findByFiltersPaginated(
        ?string $term,
        ?string $clientId,
        ?bool $isActive,
        int $page,
        int $limit,
    ): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Project::class, 'p');

        if ($term !== null) {
            $qb->andWhere('LOWER(p.name) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $term . '%');
        }

        if ($clientId !== null) {
            $qb->andWhere('CAST(p.clientId AS string) = :clientId')
                ->setParameter('clientId', $clientId);
        }

        if ($isActive !== null) {
            $qb->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        $qb->orderBy('p.name', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $items = iterator_to_array($paginator->getIterator());

        return new PaginatedResult(
            items: $items,
            totalItems: $totalItems,
            currentPage: $page,
            itemsPerPage: $limit,
        );
    }
}
