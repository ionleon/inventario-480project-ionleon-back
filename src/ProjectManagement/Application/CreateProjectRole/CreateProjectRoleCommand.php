<?php

namespace App\ProjectManagement\Application\CreateProjectRole;

final readonly class CreateProjectRoleCommand
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
