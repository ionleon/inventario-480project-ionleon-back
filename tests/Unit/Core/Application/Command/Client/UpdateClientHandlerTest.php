<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Client;

use App\Core\Application\Command\Client\UpdateClient\UpdateClientCommand;
use App\Core\Application\Command\Client\UpdateClient\UpdateClientHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Client\UpdateClient\UpdateClientServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use PHPUnit\Framework\TestCase;

final class UpdateClientHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(UpdateClientServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new UpdateClientHandler($service, $checker);
        $handler(new UpdateClientCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) ClientIdMother::create(),
            name: 'Updated Corp',
            sectorId: (string) ClientIdMother::create(),
        ));
    }
}
