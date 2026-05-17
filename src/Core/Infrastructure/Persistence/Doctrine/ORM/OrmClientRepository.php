<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Shared\Domain\Pagination\PaginatedResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final readonly class OrmClientRepository implements ClientRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Client $client): void
    {
        $this->em->persist($client);
    }

    public function remove(Client $client): void
    {
        $this->em->remove($client);
    }

    public function find(ClientId $id): ?Client
    {
        return $this->em->find(Client::class, $id);
    }

    public function findOneOrFail(ClientId $id): Client
    {
        return $this->find($id) ?? throw new ClientNotFoundException((string) $id);
    }

    public function findOneByName(ClientName $name): ?Client
    {
        return $this->em->getRepository(Client::class)->findOneBy(['name' => $name]);
    }

    /** @return list<Client> */
    public function all(): array
    {
        /** @var list<Client> */
        return $this->em->getRepository(Client::class)->findAll();
    }

    public function findByFiltersPaginated(?string $term, ?bool $isActive, int $page, int $limit): PaginatedResult
    {
        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Client::class, 'c');

        if ($term !== null) {
            $qb->andWhere('LOWER(c.name) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $term . '%');
        }

        if ($isActive !== null) {
            $qb->andWhere('c.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        $qb->orderBy('c.name', 'ASC')
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
