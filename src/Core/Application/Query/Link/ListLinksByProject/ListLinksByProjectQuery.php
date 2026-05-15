<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Link\ListLinksByProject;

use App\Core\Application\Bus\Query;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class ListLinksByProjectQuery implements Query
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $projectId,
    ) {}
}
