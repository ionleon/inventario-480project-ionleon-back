<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Link\CreateLink;

use App\Core\Application\Bus\CommandHandler;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Model\VO\Project\ProjectId;
use App\Core\Domain\Service\Link\CreateLink\CreateLinkServiceInterface;

final readonly class CreateLinkHandler implements CommandHandler
{
    public function __construct(
        private CreateLinkServiceInterface $service,
    ) {}

    public function __invoke(CreateLinkCommand $command): void
    {
        ($this->service)(
            id: new LinkId($command->id),
            projectId: new ProjectId($command->projectId),
            url: new LinkUrl($command->url),
            label: $command->label !== null ? new LinkLabel($command->label) : null,
        );
    }
}
