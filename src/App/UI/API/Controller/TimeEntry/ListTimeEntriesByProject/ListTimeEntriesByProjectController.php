<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\ListTimeEntriesByProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\TimeEntry\ListTimeEntriesByProject\ListTimeEntriesByProjectQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'TimeEntry')]
final class ListTimeEntriesByProjectController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/projects/{projectId}/time-entries', methods: ['GET'])]
    public function __invoke(string $projectId): Response
    {
        /** @var list<mixed> $entries */
        $entries = $this->queryBus->ask(new ListTimeEntriesByProjectQuery(
            securityToken: ($this->securityTokenExtractor)(),
            projectId: $projectId,
        ));

        return new JsonResponse($entries);
    }
}
