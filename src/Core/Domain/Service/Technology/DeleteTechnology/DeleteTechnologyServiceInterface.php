<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Technology\DeleteTechnology;

use App\Core\Domain\Model\VO\Technology\TechnologyId;

interface DeleteTechnologyServiceInterface
{
    public function __invoke(TechnologyId $id): void;
}
