<?php

namespace App\ProjectManagement\Application\GetTechnology;

use App\ProjectManagement\Domain\Development\Technology\Technology;
use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;

final class GetTechnologyHandler
{
    public function __construct(
        private readonly TechnologyRepositoryInterface $technologyRepository,
    ) {}

    public function handle(GetTechnologyQuery $query): Technology
    {
        $technology = $this->technologyRepository->findById($query->technologyId);

        if (!$technology) {
            throw new \DomainException('Technology not found');
        }

        return $technology;
    }
}
