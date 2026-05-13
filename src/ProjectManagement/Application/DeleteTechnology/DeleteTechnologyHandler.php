<?php

namespace App\ProjectManagement\Application\DeleteTechnology;

use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;

final class DeleteTechnologyHandler
{
    public function __construct(
        private readonly TechnologyRepositoryInterface $technologyRepository,
    ) {}

    public function handle(DeleteTechnologyCommand $command): void
    {
        $technology = $this->technologyRepository->findById($command->technologyId);

        if (!$technology) {
            throw new \DomainException('Technology not found');
        }

        if (!$technology->getDevelopments()->isEmpty()) {
            throw new \LogicException("Technology in use, cannot delete.");
        }

        $this->technologyRepository->delete($technology);
    }
}
