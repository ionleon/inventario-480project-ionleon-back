<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\ListLinksByProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Link\ListLinksByProject\ListLinksByProjectQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Link')]
final class ListLinksByProjectController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/projects/{projectId}/links', methods: ['GET'])]
    public function __invoke(string $projectId): Response
    {
        /** @var list<LinkResponse> $links */
        $links = $this->queryBus->ask(new ListLinksByProjectQuery(
            securityToken: ($this->securityTokenExtractor)(),
            projectId: $projectId,
        ));

        return new JsonResponse($links);
    }
}
