<?php

namespace App\ProjectManagement\Application\ToggleProjectUserActivation;

final readonly class ToggleProjectUserActivationCommand
{
    public function __construct(
        public string $projectId,
        public string $userId,
    ) {}
}
