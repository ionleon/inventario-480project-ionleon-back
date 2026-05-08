<?php

namespace App\Auth\Domain\Repository;

interface RefreshTokenRepositoryInterface
{

    public function revokaAllForUser(string $username): void;

    public function delete(string $tokenString): void;

}
