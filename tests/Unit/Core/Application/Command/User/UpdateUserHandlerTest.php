<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\User;

use App\Core\Application\Command\User\UpdateUser\UpdateUserCommand;
use App\Core\Application\Command\User\UpdateUser\UpdateUserHandler;
use App\Core\Domain\DTO\Security\SecurityToken;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\UpdateUser\UpdateUserServiceInterface;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use PHPUnit\Framework\TestCase;

final class UpdateUserHandlerTest extends TestCase
{
    public function test_GivenValidCommand_WhenInvoke_ThenServiceIsCalled(): void
    {
        $user = UserMother::create();

        $service = $this->createMock(UpdateUserServiceInterface::class);
        $service->expects(self::once())->method('__invoke')->willReturn($user);

        $checker = $this->createMock(SecurityChecker::class);

        $handler = new UpdateUserHandler($service, $checker);
        $handler(new UpdateUserCommand(
            securityToken: new SecurityToken('admin-id', SystemRole::ADMIN),
            id: (string) UserIdMother::create(),
            name: 'Bob',
            surname: 'Jones',
        ));
    }
}
