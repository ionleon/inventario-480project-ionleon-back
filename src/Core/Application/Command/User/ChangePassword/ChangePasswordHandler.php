<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\ChangePassword;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\User\UserId;
use App\Core\Domain\Service\Security\SecurityChecker;
use App\Core\Domain\Service\User\ChangePassword\ChangePasswordServiceInterface;

final readonly class ChangePasswordHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private ChangePasswordServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(ChangePasswordCommand $command): void
    {
        $id = new UserId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            oldPassword: $command->oldPassword,
            newPassword: $command->newPassword,
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
