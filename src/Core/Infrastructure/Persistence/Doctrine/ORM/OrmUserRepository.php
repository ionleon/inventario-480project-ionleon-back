<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Model\DTO\UserFilters;
use App\Core\Domain\Exception\User\UserNotFoundException;
use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final readonly class OrmUserRepository implements UserRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }

    public function find(UserId $id): ?User
    {
        return $this->em->find(User::class, $id);
    }

    public function findOneOrFail(UserId $id): User
    {
        return $this->find($id) ?? throw new UserNotFoundException((string) $id);
    }

    public function findOneByEmail(Email $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /** @return list<User> */
    public function all(): array
    {
        /** @var list<User> */
        return $this->em->getRepository(User::class)->findAll();
    }

    public function findByFiltersPaginated(UserFilters $filters, int $page, int $limit): PaginatedResult
    {
        $qb = $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u');

        if ($filters->term !== null) {
            $qb->andWhere('LOWER(u.name) LIKE LOWER(:term) OR LOWER(u.surname) LIKE LOWER(:term) OR LOWER(u.email) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $filters->term . '%');
        }

        if ($filters->role !== null) {
            $qb->andWhere('u.role LIKE :role')
                ->setParameter('role', '%' . $filters->role . '%');
        }

        if ($filters->isActive !== null) {
            $qb->andWhere('u.isActive = :isActive')
                ->setParameter('isActive', $filters->isActive);
        }

        $qb->orderBy('u.surname', 'ASC')
            ->addOrderBy('u.name', 'ASC')
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
