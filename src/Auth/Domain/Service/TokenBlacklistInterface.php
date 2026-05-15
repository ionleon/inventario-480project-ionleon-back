<?php

namespace App\Auth\Domain\Service;

interface TokenBlacklistInterface
{

    public function add(string $jti, int $ttl): void;
}
