<?php

namespace App\ProjectManagement\Application\ListTechnologies;

use App\ProjectManagement\Domain\Development\Technology\TechnologyRepositoryInterface;

final class ListTechnologiesHandler
{
    public function __construct(
        private readonly TechnologyRepositoryInterface $technologyRepository,
    ) {}

    public function handle(ListTechnologiesQuery $query): array
    {
        return $this->technologyRepository->findAll();
    }
}
