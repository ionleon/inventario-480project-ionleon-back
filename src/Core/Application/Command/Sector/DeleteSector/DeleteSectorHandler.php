<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Sector\DeleteSector;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Service\Sector\DeleteSector\DeleteSectorServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class DeleteSectorHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private DeleteSectorServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(DeleteSectorCommand $command): void
    {
        $id = new SectorId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
