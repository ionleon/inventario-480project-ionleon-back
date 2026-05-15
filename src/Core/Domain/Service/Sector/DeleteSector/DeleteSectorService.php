<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Sector\DeleteSector;

use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Model\VO\Sector\SectorId;

final readonly class DeleteSectorService implements DeleteSectorServiceInterface
{
    // TODO (Plan 4 — Client slice): Add cross-aggregate check here.
    // Before removing, verify no Client references this Sector. If any Client
    // exists with this sector_id, throw a DomainException blocking the deletion.
    // The check will be implemented via the ClientRepository once Client is migrated.

    public function __construct(private SectorRepository $repository) {}

    /** @throws SectorNotFoundException */
    public function __invoke(SectorId $id): void
    {
        $sector = $this->repository->findOneOrFail($id);

        $this->repository->remove($sector);
    }
}
