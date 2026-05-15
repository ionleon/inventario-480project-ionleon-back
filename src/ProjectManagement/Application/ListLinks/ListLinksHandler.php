<?php

namespace App\ProjectManagement\Application\ListLinks;

use App\ProjectManagement\Domain\Development\Link\LinkRepositoryInterface;

final class ListLinksHandler
{
    public function __construct(
        private readonly LinkRepositoryInterface $linkRepository,
    ) {}

    public function handle(ListLinksQuery $query): array
    {
        return $this->linkRepository->findAll();
    }
}
