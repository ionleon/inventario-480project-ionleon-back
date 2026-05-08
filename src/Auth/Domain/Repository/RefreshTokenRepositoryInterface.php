<?php

namespace App\Auth\Domain\Repository;

interface RefreshTokenRepositoryInterface
{

    public function revokeAllForUser(string $username): void;

    public function delete(string $tokenString): void;

}
