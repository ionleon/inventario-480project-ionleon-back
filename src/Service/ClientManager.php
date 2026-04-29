<?php

namespace App\Service;

use App\Entity\Client;
use App\Repository\SectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class ClientManager
{


    public function __construct(
        private EntityManagerInterface $em,
        private SectorRepository $sectorRepository
    ) {}

    public function create(array $data): Client
    {
        if (!isset($data['name'], $data['sector_id'])) {
            throw new \InvalidArgumentException('Name and Sector are mandatory for new clients');
        }

        $id = Uuid::fromString($data['id']);
        $sector = $this->sectorRepository->find($data['sector_id']);

        $client = new Client();
        $client->setId($id);
        $client->setName($data['name']);
        $client->setSector($sector);

        return $this->update($client, $data);
    }

    public function update(Client $client, array $data): Client
    {

        if (isset($data['name'])) {
            $client->setName($data['name']);
        }

        if (isset($data['isActive'])) {
            $client->setIsActive((bool)$data['isActive']);
        }

        if (isset($data['sector_id'])) {
            $sector = $this->sectorRepository->find($data['sector_id']);
            if (!$sector) {
                throw new NotFoundHttpException('Sector not found');
            }
            $client->setSector($sector);
        }

        if (null === $client->getSector()) {
            throw new \LogicException('A client must have a sector');
        }

        $this->em->persist($client);
        $this->em->flush();

        return $client;
    }

    public function delete(Client $client): void
    {
        $this->em->remove($client);
        $this->em->flush();
    }

    public function deactivate(Client $client): void
    {
        $client->setIsActive(false);
        $this->em->flush();
    }
}
