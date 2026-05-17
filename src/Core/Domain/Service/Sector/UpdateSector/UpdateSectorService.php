<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Sector\UpdateSector;

use App\Core\Domain\Exception\Sector\DuplicatedSectorNameException;
use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;

final readonly class UpdateSectorService implements UpdateSectorServiceInterface
{
    public function __construct(private SectorRepository $repository)
    {
    }

    /**
     * @throws SectorNotFoundException
     * @throws DuplicatedSectorNameException
     */
    public function __invoke(SectorId $id, SectorName $name): Sector
    {
        $sector = $this->repository->findOneOrFail($id);

        $existing = $this->repository->findOneByName($name);
        if ($existing !== null && !$existing->id()->equals($id)) {
            throw new DuplicatedSectorNameException((string) $name);
        }

        $sector->rename($name);

        return $sector;
    }
}
