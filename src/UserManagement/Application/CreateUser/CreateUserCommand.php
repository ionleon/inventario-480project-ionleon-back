<?php

namespace App\UserManagement\Application\CreateUser;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $surname,
        public string $role,
        public string $password,
    ) {}

}
