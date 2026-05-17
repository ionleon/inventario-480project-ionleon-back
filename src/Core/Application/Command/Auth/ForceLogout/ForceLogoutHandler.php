<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Auth\ForceLogout;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Service\RefreshToken\RevokeAllRefreshTokensForUser\RevokeAllRefreshTokensForUserServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;

final readonly class ForceLogoutHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private RevokeAllRefreshTokensForUserServiceInterface $revokeAllService,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(ForceLogoutCommand $command): void
    {
        // Force-logout is an admin-only action
        $this->checkSecurity($command->securityToken, $command->securityToken);

        ($this->revokeAllService)($command->targetUserIdentifier);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
