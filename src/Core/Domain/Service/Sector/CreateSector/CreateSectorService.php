<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Sector\CreateSector;

use App\Core\Domain\Exception\Sector\DuplicatedSectorNameException;
use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;

final readonly class CreateSectorService implements CreateSectorServiceInterface
{
    public function __construct(private SectorRepository $repository) {}

    /** @throws DuplicatedSectorNameException */
    public function __invoke(SectorId $id, SectorName $name): Sector
    {
        if ($this->repository->findOneByName($name) !== null) {
            throw new DuplicatedSectorNameException((string) $name);
        }

        $sector = Sector::create(id: $id, name: $name);
        $this->repository->add($sector);

        return $sector;
    }
}
