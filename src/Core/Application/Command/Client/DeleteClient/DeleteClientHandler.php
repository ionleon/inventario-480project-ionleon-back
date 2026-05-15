<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Client\DeleteClient;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Application\Command\Common\Security\SecurableHandler;
use App\Core\Application\Command\Common\Security\SecurityAwareTrait;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Service\Client\DeleteClient\DeleteClientServiceInterface;
use App\Core\Domain\Service\Security\SecurityChecker;

final readonly class DeleteClientHandler implements CommandHandler, SecurableHandler
{
    use SecurityAwareTrait;

    public function __construct(
        private DeleteClientServiceInterface $service,
        private SecurityChecker $securityChecker,
    ) {}

    public function __invoke(DeleteClientCommand $command): void
    {
        $id = new ClientId($command->id);

        $this->checkSecurity($command->securityToken, $id);

        ($this->service)(id: $id);
    }

    public function securityChecker(): SecurityChecker
    {
        return $this->securityChecker;
    }
}
