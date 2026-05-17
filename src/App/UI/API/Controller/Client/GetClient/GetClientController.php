<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Client\GetClient;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Client\GetClient\GetClientQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Client')]
final class GetClientController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/clients/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        /** @var GetClientResponse $response */
        $response = $this->queryBus->ask(new GetClientQuery(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new JsonResponse($response);
    }
}
