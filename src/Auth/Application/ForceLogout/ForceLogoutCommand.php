<?php

namespace App\Auth\Application\ForceLogout;

final readonly class ForceLogoutCommand
{
    public function __construct(
        public string $userIdentifier,
    ) {}
}
