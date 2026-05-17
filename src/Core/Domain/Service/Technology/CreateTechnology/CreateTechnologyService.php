<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Technology\CreateTechnology;

use App\Core\Domain\Exception\Technology\DuplicatedTechnologyNameException;
use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;

final readonly class CreateTechnologyService implements CreateTechnologyServiceInterface
{
    public function __construct(private TechnologyRepository $repository)
    {
    }

    /** @throws DuplicatedTechnologyNameException */
    public function __invoke(TechnologyId $id, TechnologyName $name): Technology
    {
        if ($this->repository->findOneByName($name) !== null) {
            throw new DuplicatedTechnologyNameException((string) $name);
        }

        $technology = Technology::create(id: $id, name: $name);
        $this->repository->add($technology);

        return $technology;
    }
}
