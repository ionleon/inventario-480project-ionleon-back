<?php

declare(strict_types=1);

namespace App\Core\Application\DTO\Security;

use App\Shared\Domain\Enum\SystemRole;

final class SecurityToken
{
    public function __construct(
        public readonly string $authUserId,
        public readonly SystemRole $role = SystemRole::EMPLOYEE,
    ) {
    }
}
