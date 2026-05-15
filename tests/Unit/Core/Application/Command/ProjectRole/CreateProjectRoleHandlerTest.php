<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\ProjectRole;

use App\Core\Application\Command\ProjectRole\CreateProjectRole\CreateProjectRoleCommand;
use App\Core\Application\Command\ProjectRole\CreateProjectRole\CreateProjectRoleHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\ProjectRole\CreateProjectRole\CreateProjectRoleServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\ProjectRole\ProjectRoleMother;
use PHPUnit\Framework\TestCase;

final class CreateProjectRoleHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $projectRole = ProjectRoleMother::create();

        $service = $this->createMock(CreateProjectRoleServiceInterface::class);
        $service->expects(self::once())->method('__invoke')->willReturn($projectRole);

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new CreateProjectRoleHandler($service, $checker);
        $handler(new CreateProjectRoleCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $projectRole->id(),
            name: (string) $projectRole->name(),
        ));
    }
}
