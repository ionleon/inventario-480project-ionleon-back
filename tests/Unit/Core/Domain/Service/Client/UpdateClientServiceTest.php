<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Client;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Exception\Client\DuplicatedClientNameException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Service\Client\UpdateClient\UpdateClientService;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientNameMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use PHPUnit\Framework\TestCase;

final class UpdateClientServiceTest extends TestCase
{
    public function test_GivenExistingClient_WhenInvoke_ThenClientIsUpdated(): void
    {
        $client = ClientMother::create();
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willReturn($client);
        $clientRepo->method('findOneByName')->willReturn(null);

        $sectorRepo = $this->createMock(SectorRepository::class);
        $sectorRepo->method('findOneOrFail')->willReturn(SectorMother::create());

        $service = new UpdateClientService($clientRepo, $sectorRepo);
        $service($client->id(), ClientNameMother::create(), SectorIdMother::create());

        // No exception means success
        $this->addToAssertionCount(1);
    }

    public function test_GivenNotFoundClient_WhenInvoke_ThenThrowsClientNotFoundException(): void
    {
        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willThrowException(new ClientNotFoundException());

        $sectorRepo = $this->createMock(SectorRepository::class);

        $this->expectException(ClientNotFoundException::class);

        (new UpdateClientService($clientRepo, $sectorRepo))(
            ClientIdMother::create(),
            ClientNameMother::create(),
            SectorIdMother::create(),
        );
    }

    public function test_GivenDuplicatedNameFromAnotherClient_WhenInvoke_ThenThrowsDuplicatedClientNameException(): void
    {
        $client = ClientMother::create();
        $anotherClient = ClientMother::create(); // different id

        $clientRepo = $this->createMock(ClientRepository::class);
        $clientRepo->method('findOneOrFail')->willReturn($client);
        $clientRepo->method('findOneByName')->willReturn($anotherClient);

        $sectorRepo = $this->createMock(SectorRepository::class);

        $this->expectException(DuplicatedClientNameException::class);

        (new UpdateClientService($clientRepo, $sectorRepo))(
            $client->id(),
            ClientNameMother::create(),
            SectorIdMother::create(),
        );
    }
}
