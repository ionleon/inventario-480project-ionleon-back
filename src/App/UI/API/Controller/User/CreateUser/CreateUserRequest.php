<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\CreateUser;

final readonly class CreateUserRequest
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $surname,
        public string $password,
        public string $role = 'ROLE_EMPLOYEE',
    ) {}
}
