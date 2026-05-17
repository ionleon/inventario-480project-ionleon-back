<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\ListTimeEntriesByUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\TimeEntry\ListTimeEntriesByUser\ListTimeEntriesByUserQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'TimeEntry')]
final class ListTimeEntriesByUserController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/users/{userId}/time-entries', methods: ['GET'])]
    public function __invoke(string $userId): Response
    {
        /** @var list<mixed> $entries */
        $entries = $this->queryBus->ask(new ListTimeEntriesByUserQuery(
            securityToken: ($this->securityTokenExtractor)(),
            userId: $userId,
        ));

        return new JsonResponse($entries);
    }
}
