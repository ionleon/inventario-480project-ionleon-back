<?php

namespace App\ProjectManagement\Application\UpdateProjectRole;

final readonly class UpdateProjectRoleCommand
{
    public function __construct(
        public string $roleId,
        public ?string $name,
    ) {}
}
