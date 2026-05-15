<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Auth;

use App\Core\Application\Command\Auth\Logout\LogoutCommand;
use App\Core\Application\Command\Auth\Logout\LogoutHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\RefreshToken\RevokeRefreshToken\RevokeRefreshTokenServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use PHPUnit\Framework\TestCase;

final class LogoutHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenRevokeServiceIsCalled(): void
    {
        $service = $this->createMock(RevokeRefreshTokenServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new LogoutHandler($service, $checker);
        $handler(new LogoutCommand(
            securityToken: new SecurityToken('user-id', SystemRole::EMPLOYEE),
            refreshToken: 'somerefreshtoken',
        ));
    }
}
