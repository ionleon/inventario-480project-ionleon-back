<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Auth\Logout;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;
use App\Core\Domain\Service\RefreshToken\RevokeRefreshToken\RevokeRefreshTokenServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class LogoutHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private RevokeRefreshTokenServiceInterface $revokeService,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(LogoutCommand $command): void
    {
        $this->checkSecurity($command->securityToken, $command->securityToken);

        ($this->revokeService)(new RefreshTokenValue($command->refreshToken));
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
