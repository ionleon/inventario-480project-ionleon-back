<?php

namespace App\UserManagement\Application\CreateUser;

use App\Shared\Domain\Enum\SystemRole;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $id,
        public string $email,
        public string $password,
        public string $name,
        public string $surname,
        public SystemRole $role,
    ) {}

}
