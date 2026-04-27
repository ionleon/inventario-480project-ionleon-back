<?php

namespace App\Service;

use App\Entity\Technology;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;

class TechnologyManager
{

    public function __construct(
        private EntityManagerInterface $em,
        private TechnologyRepository $repository
    ) {}

    public function create(array $data): Technology {
        //Añadir si pasamos ID por front
        //if (!isset($data['id'], $data['name'])) {
        if (!isset($data['name'])) {
            throw new \InvalidArgumentException('Faltan campos obligatorios (name).');
        }

        $technology = new Technology();

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
