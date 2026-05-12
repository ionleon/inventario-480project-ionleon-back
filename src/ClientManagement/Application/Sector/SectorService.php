<?php

namespace App\ClientManagement\Application\Sector;

use App\ClientManagement\Domain\Sector\Sector;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;
use App\ClientManagement\Infrastructure\Sector\DoctrineSectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;


class SectorService
{
    public function __construct(
      private readonly SectorRepositoryInterface $repository
    ) {}

    public function create(array $data) : Sector
    {
        if (!isset($data['id'], $data['name'])){
            throw new \InvalidArgumentException('ID and name are mandatory.');
        }

        $sector = new Sector();
        try{
            $sector->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e){
            throw new \InvalidArgumentException('Invalid UUID format.', $e->getCode(), $e);
        }

        return $this->update($sector, $data);
    }

    public function update(Sector $sector, array $data): Sector
    {
        $sector->setName($data['name'] ?? $sector->getName());

        $this->repository->save($sector);

        return $sector;
    }

    public function delete(Sector $sector): void
    {
        if (!$sector->getClients()->isEmpty()) {
            throw new \LogicException('Can\'t delete sectors in use.');
        }

        $this->repository->delete($sector);
    }
}
