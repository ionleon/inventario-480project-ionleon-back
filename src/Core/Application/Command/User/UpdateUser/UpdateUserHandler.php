<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\UpdateUser;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Model\VO\User\UserName;
use App\Core\Domain\Model\VO\User\UserSurname;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\UpdateUser\UpdateUserServiceInterface;

final readonly class UpdateUserHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private UpdateUserServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $id = new UserId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            name: new UserName($command->name),
            surname: new UserSurname($command->surname),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
