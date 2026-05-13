<?php

namespace App\UserManagement\Application\GetUser;

class GetUserQuery
{
    public function __construct(
        public string $userId
    ){}
}
