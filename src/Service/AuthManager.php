<?php

namespace App\Service;

use App\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\Cache\CacheInterface;



class AuthManager
{
    public function __construct(
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
        private readonly TokenStorageInterface        $tokenStorage,
        private readonly CacheInterface               $blacklistCache,
        private readonly JWTTokenManagerInterface     $jwtManager,
        private readonly RequestStack                 $requestStack,
        private readonly EntityManagerInterface       $em

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
     * @throws InvalidArgumentException
     * @throws JWTDecodeFailureException
     * @throws Exception
     */
    public function forceLogout(AppUser $user): void
    {
        try {
            $payload = $this->getPayloadFromCurrentRequest();
            if ($payload && isset($payload['jti'], $payload['exp'])) {
                $ttl = $payload['exp'] - time();
                if ($ttl > 0) {
                    $cacheItem = $this->blacklistCache->getItem('blacklist_' . $payload['jti']);
                    $cacheItem->set(true);
                    $cacheItem->expiresAfter($ttl);
                    $this->blacklistCache->save($cacheItem);
                }
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());

        }

        $this->em->createQuery('DELETE FROM App\Entity\RefreshToken r WHERE r.username = :username')
            ->setParameter('username', $user->getUserIdentifier())
            ->execute();

        $this->em->flush();
    }

    /**
     * @throws JWTDecodeFailureException
     * @throws Exception
     */
    private function getPayloadFromCurrentRequest(): ?array
    {
        $token = $this->tokenStorage->getToken();


        if ($token && method_exists($token, 'getPayload')) {
            return $token->getPayload();
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
