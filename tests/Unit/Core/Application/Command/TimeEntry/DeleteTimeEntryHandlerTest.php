<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\TimeEntry;

use App\Core\Application\Command\TimeEntry\DeleteTimeEntry\DeleteTimeEntryCommand;
use App\Core\Application\Command\TimeEntry\DeleteTimeEntry\DeleteTimeEntryHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Exception\Security\ForbiddenException;
use App\Core\Domain\Model\Repository\TimeEntryRepository;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\RoleBasedSecurityChecker;
use App\Core\Domain\Service\TimeEntry\DeleteTimeEntry\DeleteTimeEntryServiceInterface;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\TimeEntry\TimeEntryIdMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class DeleteTimeEntryHandlerTest extends TestCase
{
    public function test_GivenEmployeeDeletingOwnTimeEntry_WhenInvoke_ThenServiceIsCalled(): void
    {
        $authUserId = Uuid::v4()->toRfc4122();

        $repo = $this->createMock(TimeEntryRepository::class);
        $repo->method('findOwnerUserId')->willReturn(new UserId($authUserId));

        $service = $this->createMock(DeleteTimeEntryServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $handler = new DeleteTimeEntryHandler($service, $repo, new RoleBasedSecurityChecker());
        $handler(new DeleteTimeEntryCommand(
            securityToken: new SecurityToken($authUserId, SystemRole::EMPLOYEE),
            id: (string) TimeEntryIdMother::create(),
        ));
    }

    public function test_GivenEmployeeDeletingOtherEmployeesTimeEntry_WhenInvoke_ThenForbidden(): void
    {
        $authUserId = Uuid::v4()->toRfc4122();
        $otherUserId = Uuid::v4()->toRfc4122();

        $repo = $this->createMock(TimeEntryRepository::class);
        $repo->method('findOwnerUserId')->willReturn(new UserId($otherUserId));

        $service = $this->createMock(DeleteTimeEntryServiceInterface::class);
        $service->expects(self::never())->method('__invoke');

        $handler = new DeleteTimeEntryHandler($service, $repo, new RoleBasedSecurityChecker());

        $this->expectException(ForbiddenException::class);

        $handler(new DeleteTimeEntryCommand(
            securityToken: new SecurityToken($authUserId, SystemRole::EMPLOYEE),
            id: (string) TimeEntryIdMother::create(),
        ));
    }
}
