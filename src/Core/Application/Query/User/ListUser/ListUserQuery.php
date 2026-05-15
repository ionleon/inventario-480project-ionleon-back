<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User\ListUser;

use App\Core\Application\Bus\Query;
use App\Core\Domain\DTO\Security\SecurityToken;

final readonly class ListUserQuery implements Query
{
    public function __construct(
        public SecurityToken $securityToken,
        public ?string $term = null,
        public ?string $role = null,
        public ?bool $isActive = null,
        public int $page = 1,
        public int $limit = 10,
    ) {}
}
