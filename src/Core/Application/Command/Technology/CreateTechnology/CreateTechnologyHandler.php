<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Technology\CreateTechnology;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;
use App\Core\Domain\Service\Technology\CreateTechnology\CreateTechnologyServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class CreateTechnologyHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private CreateTechnologyServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(CreateTechnologyCommand $command): void
    {
        $id = new TechnologyId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            name: new TechnologyName($command->name),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
