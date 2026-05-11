<?php

namespace App\Service;

use App\ProjectManagement\Domain\Developments\Technology\Technology;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class TechnologyManager
{

    public function __construct(
        private EntityManagerInterface $em,
        private TechnologyRepository $repository
    ) {}

    public function create(array $data): Technology {

        if (!isset($data['id'], $data['name'])) {
            throw new \InvalidArgumentException('Faltan campos obligatorios (name).');
        }

        $technology = new Technology();
        try {
            $technology->setId(Uuid::fromString($data['id']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('UUID format invalid.');
        }

        return $this->save($technology, $data);

    }

    public function save(Technology $technology,array $data): Technology
    {
        $technology->setName($data['name'] ?? $technology->getName());
        $this->em->persist($technology);
        $this->em->flush();

        return $technology;
    }

    public function delete(Technology $technology): void {
        if (!$technology->getDevelopments()->isEmpty()) {
            throw new \LogicException('Technology in use, cannot delete.');
        }

        $this->em->remove($technology);
        $this->em->flush();
    }

}
