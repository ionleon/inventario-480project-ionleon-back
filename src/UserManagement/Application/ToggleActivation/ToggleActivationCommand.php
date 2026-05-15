<?php

namespace App\UserManagement\Application\ToggleActivation;

final class ToggleActivationCommand
{
    public function __construct(
        public string $userId
    ) {}
}
