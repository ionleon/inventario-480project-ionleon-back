<?php

namespace App\Auth\Application\ForceLogout;

use App\Auth\Domain\RefreshToken\RefreshTokenRepositoryInterface;

final class ForceLogoutHandler
{
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {}

    public function handle(ForceLogoutCommand $command): void
    {
        $this->refreshTokenRepository->revokeAllForUser($command->userIdentifier);
    }
}
