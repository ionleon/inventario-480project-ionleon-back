<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\RefreshToken\RevokeAllRefreshTokensForUser;

use App\Core\Domain\Model\Repository\RefreshTokenRepository;

final readonly class RevokeAllRefreshTokensForUserService implements RevokeAllRefreshTokensForUserServiceInterface
{
    public function __construct(private RefreshTokenRepository $repository) {}

    public function __invoke(string $username): void
    {
        $tokens = $this->repository->findByUsername($username);

        foreach ($tokens as $token) {
            $token->revoke();
            $this->repository->remove($token);
        }
    }
}
