<?php

namespace App\ClientManagement\Domain\Sector;

interface SectorRepositoryInterface
{

    public function findById(string $id): ?Sector;

    /** @return Sector[] */
    public function findAll(): array;

    public function save(Sector $sector): void;

    public function delete(Sector $sector): void;

}
