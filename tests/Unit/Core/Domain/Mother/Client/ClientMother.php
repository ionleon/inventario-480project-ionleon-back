<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Client;

use App\Core\Domain\Model\Aggregate\Client;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;

final class ClientMother
{
    public static function create(
        ?ClientId $id = null,
        ?ClientName $name = null,
        ?SectorId $sectorId = null,
    ): Client {
        return Client::create(
            id: $id ?? ClientIdMother::create(),
            name: $name ?? ClientNameMother::create(),
            sectorId: $sectorId ?? SectorIdMother::create(),
        );
    }
}
