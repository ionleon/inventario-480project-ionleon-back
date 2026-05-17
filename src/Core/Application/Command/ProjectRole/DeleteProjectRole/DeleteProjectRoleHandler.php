<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectRole\DeleteProjectRole;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Service\ProjectRole\DeleteProjectRole\DeleteProjectRoleServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class DeleteProjectRoleHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private DeleteProjectRoleServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(DeleteProjectRoleCommand $command): void
    {
        $id = new ProjectRoleId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
