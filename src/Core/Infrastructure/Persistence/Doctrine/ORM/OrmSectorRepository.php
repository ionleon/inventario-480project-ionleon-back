<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\ORM;

use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrmSectorRepository implements SectorRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Sector $sector): void
    {
        $this->em->persist($sector);
    }

    public function remove(Sector $sector): void
    {
        $this->em->remove($sector);
    }

    public function find(SectorId $id): ?Sector
    {
        return $this->em->find(Sector::class, $id);
    }

    public function findOneOrFail(SectorId $id): Sector
    {
        return $this->find($id) ?? throw new SectorNotFoundException((string) $id);
    }

    public function findOneByName(SectorName $name): ?Sector
    {
        return $this->em->getRepository(Sector::class)->findOneBy(['name' => $name]);
    }

    /** @return list<Sector> */
    public function all(): array
    {
        /** @var list<Sector> */
        return $this->em->getRepository(Sector::class)->findAll();
    }
}
