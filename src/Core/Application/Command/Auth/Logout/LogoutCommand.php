<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Auth\Logout;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class LogoutCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $refreshToken,
    ) {
    }
}
