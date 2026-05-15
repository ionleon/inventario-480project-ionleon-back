<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Link\DeleteLink;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Service\Link\DeleteLink\DeleteLinkServiceInterface;

final readonly class DeleteLinkHandler implements CommandHandler
{
    public function __construct(
        private DeleteLinkServiceInterface $service,
    ) {}

    public function __invoke(DeleteLinkCommand $command): void
    {
        ($this->service)(
            id: new LinkId($command->id),
        );
    }
}
