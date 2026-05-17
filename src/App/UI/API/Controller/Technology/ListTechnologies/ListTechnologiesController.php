<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Technology\ListTechnologies;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Technology\ListTechnologies\ListTechnologiesQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Technology')]
final class ListTechnologiesController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/technologies', methods: ['GET'])]
    public function __invoke(): Response
    {
        /** @var ListTechnologiesResponse $response */
        $response = $this->queryBus->ask(new ListTechnologiesQuery(
            securityToken: ($this->securityTokenExtractor)(),
        ));

        return new JsonResponse($response->technologies);
    }
}
