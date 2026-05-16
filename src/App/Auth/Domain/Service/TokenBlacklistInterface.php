<?php

declare(strict_types=1);

namespace App\App\Auth\Domain\Service;

interface TokenBlacklistInterface
{
    public function add(string $jti, int $ttl): void;
}
