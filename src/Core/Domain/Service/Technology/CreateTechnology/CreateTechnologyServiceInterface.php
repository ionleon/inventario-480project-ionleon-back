<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Technology\CreateTechnology;

use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;

interface CreateTechnologyServiceInterface
{
    public function __invoke(TechnologyId $id, TechnologyName $name): Technology;
}
