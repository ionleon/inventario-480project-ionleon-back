<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\User\ListUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\User\ListUser\ListUserQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User')]
final class ListUserController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/users', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        /** @var ListUserResponse $response */
        $response = $this->queryBus->ask(new ListUserQuery(
            securityToken: ($this->securityTokenExtractor)(),
            term: $request->query->get('term'),
            role: $request->query->get('role'),
            isActive: $request->query->has('is_active') ? $request->query->getBoolean('is_active') : null,
            page: $request->query->getInt('page', 1),
            limit: $request->query->getInt('limit', 10),
        ));

        return new JsonResponse($response);
    }
}
