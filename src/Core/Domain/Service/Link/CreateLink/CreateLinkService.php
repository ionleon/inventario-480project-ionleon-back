<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Link\CreateLink;

use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Model\Repository\ProjectRepository;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class CreateLinkService implements CreateLinkServiceInterface
{
    public function __construct(
        private LinkRepository $linkRepository,
        private ProjectRepository $projectRepository,
    ) {}

    public function __invoke(
        LinkId $id,
        ProjectId $projectId,
        LinkUrl $url,
        ?LinkLabel $label,
    ): Link {
        $this->projectRepository->findOneOrFail($projectId);

        $link = Link::create($id, $projectId, $url, $label);

        $this->linkRepository->add($link);

        return $link;
    }
}
