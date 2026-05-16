<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Enum\SystemRole;

final class RoleBasedSecurityChecker implements SecurityChecker
{
    public function grants(SecurityToken $securityToken, object $subject): void
    {
        // ADMIN role is handled via SecurityAwareTrait — it never reaches this method.
        // Here we only handle non-admin roles.

        $role = $securityToken->role;

        if ($role === SystemRole::EMPLOYEE) {
            // An employee may manage their own User resource
            if ($subject instanceof UserId) {
                if ((string) $subject === $securityToken->authUserId) {
                    return;
                }
                throw new ForbiddenException('user-action');
            }

            // An employee may create/edit/delete their own TimeEntries
            // (ownership is enforced further down in the domain service)
            if ($subject instanceof TimeEntryId) {
                return;
            }

            // All other write operations are ADMIN-only
            throw new ForbiddenException('role-' . $role->value);
        }

        // Default deny for any future unknown role
        throw new ForbiddenException('role-' . $role->value);
    }
}
