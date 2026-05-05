<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AuthManager
{
    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
        private readonly TagAwareCacheInterface $blacklistCache,
        private readonly EntityManagerInterface $em
    ) {}

    public function logout(string $refreshTokenString): void
    {
        $refreshToken = $this->refreshTokenManager->get($refreshTokenString);

        if ($refreshToken) {
            $this->refreshTokenManager->delete($refreshToken);
        }
    }

    public function revokeAllUserTokens(string $username): void
    {
        $tokens = $this->refreshTokenManager->getLastFromUsername($username);
    }

}
