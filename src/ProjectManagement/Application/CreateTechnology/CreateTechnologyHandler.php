<?php

namespace App\ProjectManagement\Application\CreateTechnology;

use App\ProjectManagement\Domain\Development\Technology\Technology;
use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class CreateTechnologyHandler
{
    public function __construct(
        private readonly TechnologyRepositoryInterface $technologyRepository,
    ) {}

    public function handle(CreateTechnologyCommand $command): Technology
    {
        $technology = new Technology();
        $technology->setId(Uuid::fromString($command->id));
        $technology->setName($command->name);

        $this->technologyRepository->save($technology);

        return $technology;
    }
}
