<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Client;

use App\Core\Domain\Exception\Client\DuplicatedClientNameException;
use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Aggregate\Sector;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Service\Client\CreateClient\CreateClientService;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientNameMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use PHPUnit\Framework\TestCase;

final class CreateClientServiceTest extends TestCase
{
    public function test_GivenUniqueName_WhenInvoke_ThenClientIsAdded(): void
    {
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneByName')->willReturn(null);
        $clientRepo->expects(self::once())->method('add');

        $sectorRepo = $this->createMock(SectorRepository::class);
        $sectorRepo->method('findOneOrFail')->willReturn(SectorMother::create());

        $service = new CreateClientService($clientRepo, $sectorRepo);
        $service(ClientIdMother::create(), ClientNameMother::create(), SectorIdMother::create());
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrowsDuplicatedClientNameException(): void
    {
        $existing = ClientMother::create();
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneByName')->willReturn($existing);
        $clientRepo->expects(self::never())->method('add');

        $sectorRepo = $this->createMock(SectorRepository::class);

        $this->expectException(DuplicatedClientNameException::class);

        (new CreateClientService($clientRepo, $sectorRepo))(
            ClientIdMother::create(),
            ClientNameMother::create(),
            SectorIdMother::create(),
        );
    }

    public function test_GivenSectorNotFound_WhenInvoke_ThenThrowsSectorNotFoundException(): void
    {
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneByName')->willReturn(null);

        $sectorRepo = $this->createMock(SectorRepository::class);
        $sectorRepo->method('findOneOrFail')->willThrowException(new SectorNotFoundException());

        $this->expectException(SectorNotFoundException::class);

        (new CreateClientService($clientRepo, $sectorRepo))(
            ClientIdMother::create(),
            ClientNameMother::create(),
            SectorIdMother::create(),
        );
    }
}
