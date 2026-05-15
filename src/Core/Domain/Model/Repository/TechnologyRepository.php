<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Exception\Technology\TechnologyNotFoundException;
use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;

interface TechnologyRepository
{
    public function add(Technology $technology): void;

    public function remove(Technology $technology): void;

    public function find(TechnologyId $id): ?Technology;

    /** @throws TechnologyNotFoundException */
    public function findOneOrFail(TechnologyId $id): Technology;

    public function findOneByName(TechnologyName $name): ?Technology;

    /** @return list<Technology> */
    public function all(): array;
}
