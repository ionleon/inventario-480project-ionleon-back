<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\GetUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\User\GetUser\GetUserQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User')]
final class GetUserController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/users/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        /** @var GetUserResponse $response */
        $response = $this->queryBus->ask(new GetUserQuery(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new JsonResponse($response);
    }
}
