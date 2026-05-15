<?php

namespace App\Auth\Application\Logout;

final readonly class LogoutCommand
{
    public function __construct(
        public string $refreshToken,
        public ?string $jti = null,
        public ?int $ttl = null,
    ) {}
}
