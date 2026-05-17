<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\GetSector;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Sector\GetSector\GetSectorQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Sector')]
final class GetSectorController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/sectors/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        /** @var GetSectorResponse $response */
        $response = $this->queryBus->ask(new GetSectorQuery(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new JsonResponse($response);
    }
}
