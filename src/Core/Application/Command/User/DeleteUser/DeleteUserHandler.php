<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\DeleteUser;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\DeleteUser\DeleteUserServiceInterface;

final readonly class DeleteUserHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private DeleteUserServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(DeleteUserCommand $command): void
    {
        $id = new UserId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
