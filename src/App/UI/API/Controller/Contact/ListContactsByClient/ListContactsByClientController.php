<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Contact\ListContactsByClient;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Contact\ListContactsByClient\ListContactsByClientQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Contact')]
final class ListContactsByClientController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/clients/{clientId}/contacts', methods: ['GET'])]
    public function __invoke(string $clientId): Response
    {
        /** @var ListContactsByClientResponse $response */
        $response = $this->queryBus->ask(new ListContactsByClientQuery(
            securityToken: ($this->securityTokenExtractor)(),
            clientId: $clientId,
        ));

        return new JsonResponse($response);
    }
}
