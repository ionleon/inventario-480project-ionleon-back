<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Link\DeleteLink;

use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Model\VO\Link\LinkId;

final readonly class DeleteLinkService implements DeleteLinkServiceInterface
{
    public function __construct(private LinkRepository $linkRepository) {}

    public function __invoke(LinkId $id): void
    {
        $link = $this->linkRepository->findOneOrFail($id);
        $link->delete();
        $this->linkRepository->remove($link);
    }
}
