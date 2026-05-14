<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Sector\DeleteSector;

use App\Core\Domain\Model\VO\Sector\SectorId;

interface DeleteSectorServiceInterface
{
    public function __invoke(SectorId $id): void;
}
