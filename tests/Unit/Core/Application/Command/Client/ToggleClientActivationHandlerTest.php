<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Client;

use App\Core\Application\Command\Client\ToggleClientActivation\ToggleClientActivationCommand;
use App\Core\Application\Command\Client\ToggleClientActivation\ToggleClientActivationHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Client\ToggleClientActivation\ToggleClientActivationServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Client\ClientIdMother;
use PHPUnit\Framework\TestCase;

final class ToggleClientActivationHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(ToggleClientActivationServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new ToggleClientActivationHandler($service, $checker);
        $handler(new ToggleClientActivationCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) ClientIdMother::create(),
        ));
    }
}
