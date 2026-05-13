<?php

namespace App\UserManagement\Application\UpdateUser;

use App\Shared\Domain\Enum\SystemRole;

class UpdateUserCommand
{
    public function __construct(
        public string       $userId,
        public ?string      $name       = null,
        public ?string      $surname    = null,
        public ?string      $email      = null,
        public ?SystemRole  $role       = null,
        public ?bool        $isActive   = null,
    ) {}

}
