<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Security;

use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;

interface SecurityChecker
{
    /** @throws ForbiddenException */
    public function grants(SecurityToken $securityToken, object $subject): void;
}
