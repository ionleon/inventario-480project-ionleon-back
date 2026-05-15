<?php

namespace App\Auth\Domain\RefreshToken;

interface RefreshTokenRepositoryInterface
{

    public function revokeAllForUser(string $username): void;

    public function delete(string $tokenString): void;

}
