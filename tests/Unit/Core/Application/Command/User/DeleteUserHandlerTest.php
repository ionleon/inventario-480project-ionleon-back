<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\User;

use App\Core\Application\Command\User\DeleteUser\DeleteUserCommand;
use App\Core\Application\Command\User\DeleteUser\DeleteUserHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\DeleteUser\DeleteUserServiceInterface;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use PHPUnit\Framework\TestCase;

final class DeleteUserHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $service = $this->createMock(DeleteUserServiceInterface::class);
        $service->expects(self::once())->method('__invoke');

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new DeleteUserHandler($service, $checker);
        $handler(new DeleteUserCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) UserIdMother::create(),
        ));
    }
}
