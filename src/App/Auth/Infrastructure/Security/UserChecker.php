<?php

declare(strict_types=1);

namespace App\App\Auth\Infrastructure\Security;

use App\Core\Domain\Model\Aggregate\User as AppUser;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isActive()) {
            throw new DisabledException('Account is disabled.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
