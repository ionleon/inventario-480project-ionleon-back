<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\CreateUser;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Password;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\CreateUser\CreateUserServiceInterface;
use App\Shared\Domain\Enum\SystemRole;

final readonly class CreateUserHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private CreateUserServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $id = new UserId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            email: new Email($command->email),
            name: new UserName($command->name),
            surname: new UserSurname($command->surname),
            password: new Password($command->password),
            role: SystemRole::from($command->role),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
