<?php

namespace App\Service;

use Exception;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\CacheInterface;



class AuthManager
{
    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly CacheInterface $blacklistCache,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly RequestStack $requestStack,
    ) {}

    /**
     * @throws JWTDecodeFailureException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function logout(string $refreshTokenString): void
    {
        $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
        if ($refreshToken) {
            $this->refreshTokenManager->delete($refreshToken);
        }

        $payload = $this->getPayloadFromCurrentRequest();

            if ($payload && isset($payload['jti'], $payload['exp'])) {
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

    /**
     * @throws JWTDecodeFailureException
     * @throws Exception
     */
    private function getPayloadFromCurrentRequest(): ?array
    {
        $token = $this->tokenStorage->getToken();

        if ($token) {
            return $this->jwtManager->decode($token);
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $authHeader = $request->headers->get('Authorization');
        if ($authHeader && str_starts_with($authHeader,  'Bearer ')) {
            $jwt = substr($authHeader, 7);
            try {
                return $this->jwtManager->parse($jwt);
            } catch (Exception $e) {
                throw new Exception('Parsing token error: ' . $e->getMessage());
            }
        }

        return null;
    }

    public function revokeAllUserTokens(string $username): void
    {
        $tokens = $this->refreshTokenManager->getLastFromUsername($username);
    }

}
