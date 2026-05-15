<?php

namespace App\ProjectManagement\Application\GetLink;

use App\ProjectManagement\Domain\Development\Link\Link;
use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;

final class GetLinkHandler
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
    ) {}

    public function handle(GetLinkQuery $query): Link
    {
        $link = $this->linkRepository->findById($query->linkId);

        if (!$link) {
            throw new \DomainException('Link not found');
        }

        return $link;
    }
}
