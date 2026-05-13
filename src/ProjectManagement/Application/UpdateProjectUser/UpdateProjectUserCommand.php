<?php

namespace App\ProjectManagement\Application\UpdateProjectUser;

final readonly class UpdateProjectUserCommand
{
    public function __construct(
        public string $projectId,
        public string $userId,
        public ?string $roleId,
        public ?bool $isActive,
    ) {}
}
