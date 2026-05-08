<?php

namespace App\Auth\Application;


use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Auth\Domain\TokenBlacklistInterface;
use App\Auth\Domain\TokenPayloadExtractorInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;


readonly class AuthService
{
    public function __construct(
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private TokenBlacklistInterface $blacklist,
        private TokenPayloadExtractorInterface $payloadExtractor

    ) {}

    /**
     *
     * @throws Exception
     *
     */
    public function logout(string $refreshTokenString): void
    {
       $this->refreshTokenRepository->delete($refreshTokenString);

        $payload = $this->payloadExtractor->extractFromCurrentRequest();
        if ($payload) {
            $this->blacklist->add($payload['jti'], $payload['ttl']);
        }
    }


    /**
     *
     * @throws Exception
     */
    public function forceLogout(UserInterface $user): void
    {
        $payload = $this->payloadExtractor->extractFromCurrentRequest();
        if ($payload) {
            $this->blacklist->add($payload['jti'], $payload['ttl']);
        }

        $this->refreshTokenRepository->revokeAllForUser($user->getUserIdentifier());
    }

}
