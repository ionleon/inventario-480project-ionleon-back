<?php

namespace App\Service;

use App\Entity\Sector;
use App\Repository\SectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;


class SectorManager
{
    public function __construct(
      private EntityManagerInterface $em,
      private SectorRepository $repository
    ) {}

    public function create(array $data) : Sector
    {
        if (!isset($data['id'], $data['name'])){
            throw new \InvalidArgumentException('El ID y el nombre del sector son obligatorios.');
        }

        $sector = new Sector();
        try{
            $sector->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e){
            throw new \INvalidArgumentException('Invalid UUID format.');
        }

        return $this->save($sector, $data);
    }

    public function save(Sector $sector, array $data): Sector
    {
        $sector->setName($data['name'] ?? $sector->getName());

        $this->em->persist($sector);
        $this->em->flush();

        return $sector;
    }

    public function delete(Sector $sector): void
    {
        if (!$sector->getClients()->isEmpty()) {
            throw new \LogicException('Can\'t delete sectors in use.');
        }

        $this->em->remove($sector);
        $this->em->flush();
    }
}
