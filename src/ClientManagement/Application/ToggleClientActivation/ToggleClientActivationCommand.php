<?php

namespace App\ClientManagement\Application\ToggleClientActivation;

final readonly class ToggleClientActivationCommand
{
    public function __construct(
        public string $clientId,
    ) {}
}
