<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Link\GetLink;

use App\App\UI\API\Controller\Link\GetLink\GetLinkResponse;
use App\Core\Application\Bus\QueryHandler;
use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Model\VO\Link\LinkId;

final readonly class GetLinkHandler implements QueryHandler
{
    public function __construct(private LinkRepository $repository) {}

    public function __invoke(GetLinkQuery $query): GetLinkResponse
    {
        $link = $this->repository->findOneOrFail(new LinkId($query->id));

        return GetLinkResponse::from($link);
    }
}
