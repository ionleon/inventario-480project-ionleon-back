<?php

namespace App\UserManagement\Domain\Event;


final readonly class UserActivationChanged
{
    public function __construct(
        public string $userId,
        public bool   $isActive,
    ) {}
}
