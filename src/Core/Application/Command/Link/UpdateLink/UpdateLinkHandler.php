<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Link\UpdateLink;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Service\Link\UpdateLink\UpdateLinkServiceInterface;

final readonly class UpdateLinkHandler implements CommandHandler
{
    public function __construct(
        private UpdateLinkServiceInterface $service,
    ) {}

    public function __invoke(UpdateLinkCommand $command): void
    {
        ($this->service)(
            id: new LinkId($command->id),
            url: new LinkUrl($command->url),
            label: $command->label !== null ? new LinkLabel($command->label) : null,
        );
    }
}
