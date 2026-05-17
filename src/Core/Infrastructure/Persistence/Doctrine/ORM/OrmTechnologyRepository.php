<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\Technology\TechnologyNotFoundException;
use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmTechnologyRepository implements TechnologyRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Technology $technology): void
    {
        $this->em->persist($technology);
    }

    public function remove(Technology $technology): void
    {
        $this->em->remove($technology);
    }

    public function find(TechnologyId $id): ?Technology
    {
        return $this->em->find(Technology::class, $id);
    }

    public function findOneOrFail(TechnologyId $id): Technology
    {
        return $this->find($id) ?? throw new TechnologyNotFoundException((string) $id);
    }

    public function findOneByName(TechnologyName $name): ?Technology
    {
        return $this->em->getRepository(Technology::class)->findOneBy(['name' => $name]);
    }

    /** @return list<Technology> */
    public function all(): array
    {
        /** @var list<Technology> */
        return $this->em->getRepository(Technology::class)->findAll();
    }
}
