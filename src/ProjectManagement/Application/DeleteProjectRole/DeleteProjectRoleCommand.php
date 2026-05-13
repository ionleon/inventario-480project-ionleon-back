<?php

namespace App\ProjectManagement\Application\DeleteProjectRole;

final readonly class DeleteProjectRoleCommand
{
    public function __construct(
        public string $roleId,
    ) {}
}
