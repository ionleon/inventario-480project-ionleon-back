<?php

namespace App\Auth\Application;


use App\Auth\Domain\RefreshToken\RefreshTokenRepositoryInterface;
use App\Auth\Domain\Service\TokenBlacklistInterface;
use App\Auth\Domain\Service\TokenPayloadExtractorInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;


readonly class AuthService
{
    public function __construct(
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private TokenBlacklistInterface         $blacklist
    ) {}

    /**
     *
     * @throws Exception
     *
     */
    public function logout(string $refreshTokenString, string $jti, int $ttl): void
    {
       $this->refreshTokenRepository->delete($refreshTokenString);
       $this->blacklist->add($jti, $ttl);
    }


    /**
     *
     * @throws Exception
     */
    public function forceLogout(UserInterface $user): void
    {
        $this->refreshTokenRepository->revokeAllForUser($user->getUserIdentifier());
    }

}
