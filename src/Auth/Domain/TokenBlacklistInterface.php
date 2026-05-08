<?php

namespace App\Auth\Domain;

interface TokenBlacklistInterface
{

    public function add(string $jti, int $ttl): void;
}
