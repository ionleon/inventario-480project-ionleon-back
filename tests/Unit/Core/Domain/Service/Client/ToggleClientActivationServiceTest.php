<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Client;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Service\Client\ToggleClientActivation\ToggleClientActivationService;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use PHPUnit\Framework\TestCase;

final class ToggleClientActivationServiceTest extends TestCase
{
    public function test_GivenActiveClient_WhenInvoke_ThenClientIsDeactivated(): void
    {
        $client = ClientMother::create();
        self::assertTrue($client->isActive());

        $repo = $this->createMock(ClientRepository::class);
        $repo->method('findOneOrFail')->willReturn($client);

        (new ToggleClientActivationService($repo))($client->id());

        self::assertFalse($client->isActive());
    }

    public function test_GivenNotFoundClient_WhenInvoke_ThenThrowsClientNotFoundException(): void
    {
        $repo = $this->createMock(ClientRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new ClientNotFoundException());

        $this->expectException(ClientNotFoundException::class);

        (new ToggleClientActivationService($repo))(ClientIdMother::create());
    }
}
