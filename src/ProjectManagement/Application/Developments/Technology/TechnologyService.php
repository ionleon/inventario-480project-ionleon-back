<?php

namespace App\ProjectManagement\Application\Developments\Technology;

use App\ProjectManagement\Domain\Developments\Technology\Technology;
use App\ProjectManagement\Domain\Developments\Technology\TechnologyRepositoryInterface;
use App\ProjectManagement\Infrastructure\Developments\Technology\DoctrineTechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class TechnologyService
{

    public function __construct(
        private TechnologyRepositoryInterface $repository
    ) {}

    public function create(array $data): Technology {

        if (!isset($data['id'], $data['name'])) {
            throw new \InvalidArgumentException('Missing mandatory fields (id, name).');
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
        $this->repository->save($technology);

        return $technology;
    }

    public function delete(Technology $technology): void {
        if (!$technology->getDevelopments()->isEmpty()) {
            throw new \LogicException('Technology in use, cannot delete.');
        }

        $this->repository->delete($technology);
    }

}
