<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProjectRole\ListProjectRoles;

use App\Core\Application\Bus\Query;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class ListProjectRolesQuery implements Query
{
    public function __construct(
        public SecurityToken $securityToken,
    ) {}
}
