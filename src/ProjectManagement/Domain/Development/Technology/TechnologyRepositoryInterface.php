<?php

namespace App\ProjectManagement\Domain\Development\Technology;

interface TechnologyRepositoryInterface
{

    public function findById(string $id): ?Technology;
    public function findAll(): array;

    public function save(Technology $technology): void;

    public function delete(Technology $technology): void;

}
