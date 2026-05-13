<?php

namespace App\ProjectManagement\Application\UpdateTechnology;

use App\ProjectManagement\Domain\Development\Technology\Technology;
use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;

final class UpdateTechnologyHandler
{
    public function __construct(
        private readonly TechnologyRepositoryInterface $technologyRepository,
    ) {}

    public function handle(UpdateTechnologyCommand $command): Technology
    {
        $technology = $this->technologyRepository->findById($command->technologyId);

        if (!$technology) {
            throw new \DomainException('Technology not found');
        }

        if ($command->name !== null) {
            $technology->setName($command->name);
        }

        $this->technologyRepository->save($technology);

        return $technology;
    }
}
