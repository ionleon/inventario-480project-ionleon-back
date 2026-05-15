<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\RefreshToken\RevokeRefreshToken;

use App\Core\Domain\Exception\RefreshToken\RefreshTokenNotFoundException;
use App\Core\Domain\Model\Repository\RefreshTokenRepository;
use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;

final readonly class RevokeRefreshTokenService implements RevokeRefreshTokenServiceInterface
{
    public function __construct(private RefreshTokenRepository $repository) {}

    /** @throws RefreshTokenNotFoundException */
    public function __invoke(RefreshTokenValue $value): void
    {
        $token = $this->repository->findOneByValue($value);

        if ($token === null) {
            throw new RefreshTokenNotFoundException((string) $value);
        }

        $token->revoke();
        $this->repository->remove($token);
    }
}
