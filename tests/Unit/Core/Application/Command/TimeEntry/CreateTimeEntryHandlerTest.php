<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\TimeEntry;

use App\Core\Application\Command\TimeEntry\CreateTimeEntry\CreateTimeEntryCommand;
use App\Core\Application\Command\TimeEntry\CreateTimeEntry\CreateTimeEntryHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Service\Security\RoleBasedSecurityChecker;
use App\Core\Domain\Service\TimeEntry\CreateTimeEntry\CreateTimeEntryServiceInterface;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Project\ProjectIdMother;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryIdMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CreateTimeEntryHandlerTest extends TestCase
{
    public function test_GivenAdmin_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(CreateTimeEntryServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $handler = new CreateTimeEntryHandler($service, new RoleBasedSecurityChecker());
        $handler(new CreateTimeEntryCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) TimeEntryIdMother::create(),
            projectId: (string) ProjectIdMother::create(),
            userId: Uuid::v4()->toRfc4122(),
            date: '2026-05-15',
            hours: '8.00',
            description: null,
        ));
    }

    public function test_GivenEmployeeCreatingOwnTimeEntry_WhenInvoke_ThenServiceIsCalled(): void
    {
        $authUserId = Uuid::v4()->toRfc4122();
        $service = $this->createMock(CreateTimeEntryServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $handler = new CreateTimeEntryHandler($service, new RoleBasedSecurityChecker());
        $handler(new CreateTimeEntryCommand(
            securityToken: new SecurityToken($authUserId, SystemRole::EMPLOYEE),
            id: (string) TimeEntryIdMother::create(),
            projectId: (string) ProjectIdMother::create(),
            userId: $authUserId,
            date: '2026-05-15',
            hours: '8.00',
            description: null,
        ));
    }

    public function test_GivenEmployeeCreatingTimeEntryForOtherUser_WhenInvoke_ThenForbidden(): void
    {
        $service = $this->createMock(CreateTimeEntryServiceInterface::class);
        $service->expects(self::never())->method('__invoke');

        $handler = new CreateTimeEntryHandler($service, new RoleBasedSecurityChecker());

        $this->expectException(ForbiddenException::class);

        $handler(new CreateTimeEntryCommand(
            securityToken: new SecurityToken(Uuid::v4()->toRfc4122(), SystemRole::EMPLOYEE),
            id: (string) TimeEntryIdMother::create(),
            projectId: (string) ProjectIdMother::create(),
            userId: Uuid::v4()->toRfc4122(),
            date: '2026-05-15',
            hours: '8.00',
            description: null,
        ));
    }
}
