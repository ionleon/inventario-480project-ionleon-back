<?php

namespace App\Service;

use App\Entity\Client;
use App\Repository\SectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private SectorRepository $sectorRepository
    ) {}

    public function create(array $data): Client
    {
        $client = new Client();
        return $this->update($client, $data);
    }

    public function update(Client $client, array $data): Client
    {
        if (isset($data['name'])) {
            $client->setName($data['name'] ?? $client->getName());
        }

        if (isset($data['isActive'])) {
            $client->setIsActive($data['isActive'] ?? $client->isActive() ?? true);
        }

        if (isset($data['sector_id'])) {
            $sector = $this->sectorRepository->find($data['sector_id']);
            if (!$sector) {
                throw new NotFoundHttpException('Sector not found');
            }
            $client->setSector($sector);
        } elseif (!$client->getSector()) {
            throw new \InvalidArgumentException('sector_id is required');
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
