<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Auth;

use App\Core\Application\Command\Auth\ForceLogout\ForceLogoutCommand;
use App\Core\Application\Command\Auth\ForceLogout\ForceLogoutHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\RefreshToken\RevokeAllRefreshTokensForUser\RevokeAllRefreshTokensForUserServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use PHPUnit\Framework\TestCase;

final class ForceLogoutHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenRevokeAllServiceIsCalled(): void
    {
        $service = $this->createMock(RevokeAllRefreshTokensForUserServiceInterface::class);
        $service->expects(self::once())
            ->method('__invoke')
            ->with('target@example.com');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new ForceLogoutHandler($service, $checker);
        $handler(new ForceLogoutCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            targetUserIdentifier: 'target@example.com',
        ));
    }
}
