<?php

namespace App\ProjectManagement\Application\ToggleProjectActivation;

final readonly class ToggleProjectActivationCommand
{
    public function __construct(
        public string $projectId,
    ) {}
}
