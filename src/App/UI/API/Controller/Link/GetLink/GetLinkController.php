<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Link\GetLink;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Link\GetLink\GetLinkQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Link')]
final class GetLinkController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/links/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        /** @var GetLinkResponse $response */
        $response = $this->queryBus->ask(new GetLinkQuery(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new JsonResponse($response);
    }
}
