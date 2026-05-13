<?php

namespace App\ProjectManagement\Application\GetProjectRole;

final readonly class GetProjectRoleQuery
{
    public function __construct(
        public string $roleId,
    ) {}
}
