<?php

namespace App\ProjectManagement\Application\DeleteLink;

use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;

final class DeleteLinkHandler
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
    ) {}

    public function handle(DeleteLinkCommand $command): void
    {
        $link = $this->linkRepository->findById($command->linkId);

        if (!$link) {
            throw new \DomainException('Link not found');
        }

        $this->linkRepository->delete($link);
    }
}
