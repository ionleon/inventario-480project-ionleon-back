<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Sector\GetSector;

use App\Core\Application\Bus\Query;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class GetSectorQuery implements Query
{
    public function __construct(
        public SecurityToken $securityToken,
        public string $id,
    ) {
    }
}
