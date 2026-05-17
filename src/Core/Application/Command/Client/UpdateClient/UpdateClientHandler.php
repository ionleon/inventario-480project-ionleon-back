<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Client\UpdateClient;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;
use App\Core\Domain\Service\Client\UpdateClient\UpdateClientServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class UpdateClientHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private UpdateClientServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {
    }

    public function __invoke(UpdateClientCommand $command): void
    {
        $id = new ClientId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(
            id: $id,
            name: new ClientName($command->name),
            sectorId: new SectorId($command->sectorId),
        );
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
