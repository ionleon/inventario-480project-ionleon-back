<?php

namespace App\Auth\Application\Logout;

use App\Auth\Domain\RefreshToken\RefreshTokenRepositoryInterface;
use App\Auth\Domain\Service\TokenBlacklistInterface;

final class LogoutHandler
{
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly TokenBlacklistInterface $blacklist,
    ) {}

    public function handle(LogoutCommand $command): void
    {
        $this->refreshTokenRepository->delete($command->refreshToken);

        if ($command->jti && $command->ttl) {
            $this->blacklist->add($command->jti, $command->ttl);
        }
    }
}
