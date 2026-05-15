<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\User;

use App\Core\Application\Command\User\CreateUser\CreateUserCommand;
use App\Core\Application\Command\User\CreateUser\CreateUserHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\CreateUser\CreateUserServiceInterface;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use PHPUnit\Framework\TestCase;

final class CreateUserHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $user = UserMother::create();

        $service = $this->createMock(CreateUserServiceInterface::class);
        $service->expects(self::once())->method('__invoke')->willReturn($user);

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new CreateUserHandler($service, $checker);
        $handler(new CreateUserCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) $user->id(),
            email: (string) $user->email(),
            name: (string) $user->name(),
            surname: (string) $user->surname(),
            password: '$2y$10$hash',
            role: SystemRole::EMPLOYEE->value,
        ));
    }
}
