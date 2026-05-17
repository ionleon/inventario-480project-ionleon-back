<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Sector\ListSectors;

use App\Core\Application\Bus\Query;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class ListSectorsQuery implements Query
{
    public function __construct(
        public SecurityToken $securityToken,
    ) {
    }
}
