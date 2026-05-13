<?php

namespace App\UserManagement\Application\DeleteUser;

final readonly class DeleteUserCommand
{
    public function __construct(
        public string $userId,
    ) {}

}
