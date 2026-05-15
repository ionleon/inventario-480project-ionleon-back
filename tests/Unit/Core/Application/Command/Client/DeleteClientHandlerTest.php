<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Client;

use App\Core\Application\Command\Client\DeleteClient\DeleteClientCommand;
use App\Core\Application\Command\Client\DeleteClient\DeleteClientHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Client\DeleteClient\DeleteClientServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use PHPUnit\Framework\TestCase;

final class DeleteClientHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(DeleteClientServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new DeleteClientHandler($service, $checker);
        $handler(new DeleteClientCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) ClientIdMother::create(),
        ));
    }
}
