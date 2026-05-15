<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\CreateUser;

use App\Core\Application\Bus\Command;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class CreateUserCommand implements Command
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
        public string $email,
        public string $name,
        public string $surname,
        public string $password,
        public string $role,
    ) {}
}
