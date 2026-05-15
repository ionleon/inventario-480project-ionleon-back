<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\GetProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Project\GetProject\GetProjectQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project')]
final class GetProjectController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/projects/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        /** @var GetProjectResponse $response */
        $response = $this->queryBus->ask(new GetProjectQuery(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new JsonResponse($response);
    }
}
