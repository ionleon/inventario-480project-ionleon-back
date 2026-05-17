<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Enum\SystemRole;

final class RoleBasedSecurityChecker implements SecurityChecker
{
    public function grants(SecurityToken $securityToken, object $subject): void
    {
        // ADMIN bypasses in SecurityAwareTrait; only non-admin roles reach here.
        $role = $securityToken->role;

        if ($role === SystemRole::EMPLOYEE) {
            // EMPLOYEE may only act on resources owned by themselves. Handlers must pass
            // a UserId representing the owner (own user for User aggregate operations,
            // TimeEntry's owning user via TimeEntryRepository::findOwnerUserId, etc.).
            if ($subject instanceof UserId) {
                if ((string) $subject === $securityToken->authUserId) {
                    return;
                }
                throw new ForbiddenException('owner-mismatch');
            }

            // No other subject type is allowed for EMPLOYEE.
            throw new ForbiddenException('role-' . $role->value);
        }

        // Default deny for any future unknown role.
        throw new ForbiddenException('role-' . $role->value);
    }
}
