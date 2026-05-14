<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Common\Security;

use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Application\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SecurityAwareTraitTest extends TestCase
{
    public function test_GivenAdminToken_WhenCheckSecurity_ThenCheckerIsBypassed(): void
    {
        $checker = $this->createMock(SecurityChecker::class);
        $checker->expects(self::never())->method('grants');

        $handler = $this->makeHandler($checker);
        $token = new SecurityToken('id', SystemRole::ADMIN);

        $handler->checkSecurity($token, new stdClass());
    }

    public function test_GivenEmployeeToken_WhenCheckSecurity_ThenCheckerIsCalled(): void
    {
        $checker = $this->createMock(SecurityChecker::class);
        $checker->expects(self::once())->method('grants');

        $handler = $this->makeHandler($checker);
        $token = new SecurityToken('id', SystemRole::EMPLOYEE);

        $handler->checkSecurity($token, new stdClass());
    }

    private function makeHandler(SecurityChecker $checker): SecurableHandler
    {
        return new class($checker) implements SecurableHandler {
            use SecurityAwareTrait;
            public function __construct(private readonly SecurityChecker $checker) {}
            public function securityChecker(): SecurityChecker { return $this->checker; }
        };
    }
}
