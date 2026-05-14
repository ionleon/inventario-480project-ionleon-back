<?php

declare(strict_types=1);

namespace App\App\Auth\Infrastructure;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Shared\Domain\Enum\SystemRole;
use App\UserManagement\Domain\AppUser;
use Symfony\Bundle\SecurityBundle\Security;

final class JwtSecurityTokenExtractor implements SecurityTokenExtractorInterface
{
    public function __construct(private readonly Security $security) {}

    public function __invoke(): SecurityToken
    {
        $user = $this->security->getUser();

        if (!$user instanceof AppUser) {
            return new SecurityToken('anonymous', SystemRole::EMPLOYEE);
        }

        return new SecurityToken(
            authUserId: (string) $user->getId(),
            role: $user->getRole(),
        );
    }
}
