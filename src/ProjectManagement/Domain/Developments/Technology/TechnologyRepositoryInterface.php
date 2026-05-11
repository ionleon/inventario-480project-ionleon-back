<?php

namespace App\ProjectManagement\Domain\Developments\Technology;

interface TechnologyRepositoryInterface
{

    public function findById(string $id): ?Technology;
    public function findAll(): array;

    public function save(Technology $technology): void;

    public function delete(Technology $technology): void;

}
