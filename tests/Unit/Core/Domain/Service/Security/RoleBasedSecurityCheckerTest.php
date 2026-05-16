<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Security;

use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Model\VO\TimeEntry\TimeEntryId;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\RoleBasedSecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class RoleBasedSecurityCheckerTest extends TestCase
{
    private RoleBasedSecurityChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new RoleBasedSecurityChecker();
    }

    public function test_GivenEmployeeActingOnOwnUserId_WhenGrants_ThenAllowed(): void
    {
        $userId = Uuid::v4()->toRfc4122();
        $token = new SecurityToken(authUserId: $userId, role: SystemRole::EMPLOYEE);
        $subject = new UserId($userId);

        // Must not throw
        $this->checker->grants($token, $subject);
        $this->addToAssertionCount(1);
    }

    public function test_GivenEmployeeActingOnOtherUserId_WhenGrants_ThenForbiddenException(): void
    {
        $token = new SecurityToken(
            authUserId: Uuid::v4()->toRfc4122(),
            role: SystemRole::EMPLOYEE,
        );
        $otherUserId = new UserId(Uuid::v4()->toRfc4122());

        $this->expectException(ForbiddenException::class);

        $this->checker->grants($token, $otherUserId);
    }

    public function test_GivenEmployeeActingOnTimeEntryId_WhenGrants_ThenAllowed(): void
    {
        $token = new SecurityToken(
            authUserId: Uuid::v4()->toRfc4122(),
            role: SystemRole::EMPLOYEE,
        );
        $subject = new TimeEntryId(Uuid::v4()->toRfc4122());

        // Must not throw
        $this->checker->grants($token, $subject);
        $this->addToAssertionCount(1);
    }

    public function test_GivenEmployeeActingOnArbitraryObject_WhenGrants_ThenForbiddenException(): void
    {
        $token = new SecurityToken(
            authUserId: Uuid::v4()->toRfc4122(),
            role: SystemRole::EMPLOYEE,
        );
        $subject = new \stdClass();

        $this->expectException(ForbiddenException::class);

        $this->checker->grants($token, $subject);
    }
}
