<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\ListProjects;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Project\ListProjects\ListProjectsQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project')]
final class ListProjectsController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/projects', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        /** @var ListProjectsResponse $response */
        $response = $this->queryBus->ask(new ListProjectsQuery(
            securityToken: ($this->securityTokenExtractor)(),
            term: $request->query->get('term'),
            clientId: $request->query->get('client_id'),
            isActive: $request->query->has('is_active') ? $request->query->getBoolean('is_active') : null,
            page: $request->query->getInt('page', 1),
            limit: $request->query->getInt('limit', 10),
        ));

        return new JsonResponse($response);
    }
}
