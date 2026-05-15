<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Client;

use App\Core\Domain\Exception\Client\ClientNotFoundException;
use App\Core\Domain\Model\Repository\ClientRepository;
use App\Core\Domain\Service\Client\DeleteClient\DeleteClientService;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientMother;
use PHPUnit\Framework\TestCase;

final class DeleteClientServiceTest extends TestCase
{
    public function test_GivenExistingClient_WhenInvoke_ThenClientIsRemoved(): void
    {
        $client = ClientMother::create();
        $repo = $this->createMock(ClientRepository::class);
        $repo->method('findOneOrFail')->willReturn($client);
        $repo->expects(self::once())->method('remove');

        (new DeleteClientService($repo))($client->id());
    }

    public function test_GivenNotFoundClient_WhenInvoke_ThenThrowsClientNotFoundException(): void
    {
        $repo = $this->createMock(ClientRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new ClientNotFoundException());

        $this->expectException(ClientNotFoundException::class);

        (new DeleteClientService($repo))(ClientIdMother::create());
    }
}
