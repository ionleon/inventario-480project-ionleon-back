<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Repository;

use App\Core\Domain\Model\Aggregate\RefreshToken;
use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;

interface RefreshTokenRepository
{
    public function add(RefreshToken $refreshToken): void;

    public function remove(RefreshToken $refreshToken): void;

    public function findById(int $id): ?RefreshToken;

    public function findOneByValue(RefreshTokenValue $value): ?RefreshToken;

    /** @return list<RefreshToken> */
    public function findByUsername(string $username): array;
}
