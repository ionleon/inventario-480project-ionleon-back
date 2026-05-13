<?php

namespace App\ClientManagement\Infrastructure\Sector;

use App\ClientManagement\Domain\Sector\Sector;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sector>
 */
class DoctrineSectorRepository extends ServiceEntityRepository implements SectorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sector::class);
    }

    public function findById(string $id): ?Sector
    {
        return $this->find($id);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(Sector $sector): void
    {
        $this->getEntityManager()->persist($sector);
        $this->getEntityManager()->flush();
    }

    public function delete(Sector $sector): void
    {
        $this->getEntityManager()->remove($sector);
        $this->getEntityManager()->flush();
    }
}
