<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\ProjectRole;

use App\Core\Application\Command\ProjectRole\DeleteProjectRole\DeleteProjectRoleCommand;
use App\Core\Application\Command\ProjectRole\DeleteProjectRole\DeleteProjectRoleHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\ProjectRole\DeleteProjectRole\DeleteProjectRoleServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use PHPUnit\Framework\TestCase;

final class DeleteProjectRoleHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $projectRole = ProjectRoleMother::create();

        $service = $this->createMock(DeleteProjectRoleServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new DeleteProjectRoleHandler($service, $checker);
        $handler(new DeleteProjectRoleCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $projectRole->id(),
        ));
    }
}
