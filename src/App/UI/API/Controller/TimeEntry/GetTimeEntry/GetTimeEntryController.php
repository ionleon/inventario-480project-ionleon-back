<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\TimeEntry\GetTimeEntry;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\TimeEntry\GetTimeEntry\GetTimeEntryQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'TimeEntry')]
final class GetTimeEntryController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/time-entries/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        /** @var TimeEntryResponse $timeEntry */
        $timeEntry = $this->queryBus->ask(new GetTimeEntryQuery(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new JsonResponse($timeEntry);
    }
}
