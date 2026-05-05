<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AuthManager
{
    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly TagAwareCacheInterface $blacklistCache,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly EntityManagerInterface $em
    ) {}

    public function logout(string $refreshTokenString): void
    {
        $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
        if ($refreshToken) {
            $this->refreshTokenManager->delete($refreshToken);
        }

        $token = $this->tokenStorage->getToken();

        if ($token) {
            $payload = $this->jwtManager->decode($token);
            if (isset($payload['jti'], $payload['exp'])) {
                $jti = $payload['jti'];
                $expiration = $payload['exp'];
                $ttl = $expiration - time();

                if ($ttl > 0) {
                    $cacheItem = $this->blacklistCache->getItem('blacklist_' . $jti);
                    $cacheItem->set(true);
                    $cacheItem->expiresAfter($ttl);
                    $this->blacklistCache->save($cacheItem);
                }
            }

        }

    }

    public function revokeAllUserTokens(string $username): void
    {
        $tokens = $this->refreshTokenManager->getLastFromUsername($username);
    }

}
