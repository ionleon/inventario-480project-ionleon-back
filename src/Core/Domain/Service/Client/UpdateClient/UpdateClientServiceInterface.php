<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Client\UpdateClient;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Exception\Client\DuplicatedClientNameException;
use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;

interface UpdateClientServiceInterface
{
    /**
     * @throws ClientNotFoundException
     * @throws DuplicatedClientNameException
     * @throws SectorNotFoundException
     */
    public function __invoke(ClientId $id, ClientName $name, SectorId $sectorId): void;
}
