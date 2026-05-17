<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Sector\UpdateSector;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Model\VO\Sector\SectorName;
use App\Core\Domain\Service\Sector\UpdateSector\UpdateSectorServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class UpdateSectorHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private UpdateSectorServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(UpdateSectorCommand $command): void
    {
        $id = new SectorId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            name: new SectorName($command->name),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
