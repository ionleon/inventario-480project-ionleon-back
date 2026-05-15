<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Technology\DeleteTechnology;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Service\Technology\DeleteTechnology\DeleteTechnologyServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class DeleteTechnologyHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private DeleteTechnologyServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(DeleteTechnologyCommand $command): void
    {
        $id = new TechnologyId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
