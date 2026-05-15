<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProjectRole\CreateProjectRole;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleId;
use App\Core\Domain\Model\VO\ProjectRole\ProjectRoleName;
use App\Core\Domain\Service\ProjectRole\CreateProjectRole\CreateProjectRoleServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class CreateProjectRoleHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private CreateProjectRoleServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(CreateProjectRoleCommand $command): void
    {
        $id = new ProjectRoleId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            name: new ProjectRoleName($command->name),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
