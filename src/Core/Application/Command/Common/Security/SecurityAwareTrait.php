<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Common\Security;

use App\Core\Domain\DTO\Security\SecurityToken;
use App\Shared\Domain\Enum\SystemRole;

trait SecurityAwareTrait
{
    public function checkSecurity(SecurityToken $securityToken, object $subject): void
    {
        if ($securityToken->role === SystemRole::ADMIN) {
            return;
        }

        $this->securityChecker()->grants($securityToken, $subject);
    }
}
