<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Domain\DTO\Security\SecurityToken;

final class PermissiveSecurityChecker implements SecurityChecker
{
    public function grants(SecurityToken $securityToken, object $subject): void
    {
        // Provisional: permite todo. Se sustituye en Plan 6 (Fase 3).
    }
}
