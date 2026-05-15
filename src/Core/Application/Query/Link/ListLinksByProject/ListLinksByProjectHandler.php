<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Link\ListLinksByProject;

use App\App\UI\API\Controller\Link\ListLinksByProject\LinkResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Aggregate\Link;
use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Model\VO\Project\ProjectId;

final readonly class ListLinksByProjectHandler implements QueryHandler
{
    public function __construct(private LinkRepository $repository) {}

    /** @return list<LinkResponse> */
    public function __invoke(ListLinksByProjectQuery $query): array
    {
        $links = $this->repository->findByProject(new ProjectId($query->projectId));

        return array_map(
            static fn(Link $link) => LinkResponse::from($link),
            $links,
        );
    }
}
