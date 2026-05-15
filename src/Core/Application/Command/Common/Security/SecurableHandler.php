<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Common\Security;

use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Service\Security\SecurityChecker;

interface SecurableHandler
{
    public function securityChecker(): SecurityChecker;

    /** @throws ForbiddenException */
    public function checkSecurity(SecurityToken $securityToken, object $subject): void;
}
