<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Client;

use App\Core\Application\Command\Client\CreateClient\CreateClientCommand;
use App\Core\Application\Command\Client\CreateClient\CreateClientHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Service\Client\CreateClient\CreateClientServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use PHPUnit\Framework\TestCase;

final class CreateClientHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(CreateClientServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new CreateClientHandler($service, $checker);
        $handler(new CreateClientCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) ClientIdMother::create(),
            name: 'Acme Corp',
            sectorId: (string) ClientIdMother::create(),
        ));
    }
}
