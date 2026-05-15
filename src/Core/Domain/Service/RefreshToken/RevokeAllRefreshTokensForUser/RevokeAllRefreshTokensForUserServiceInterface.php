<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\RefreshToken\RevokeAllRefreshTokensForUser;

interface RevokeAllRefreshTokensForUserServiceInterface
{
    public function __invoke(string $username): void;
}
