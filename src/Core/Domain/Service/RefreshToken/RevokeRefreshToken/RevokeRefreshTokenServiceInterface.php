<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\RefreshToken\RevokeRefreshToken;

use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;

interface RevokeRefreshTokenServiceInterface
{
    public function __invoke(RefreshTokenValue $value): void;
}
