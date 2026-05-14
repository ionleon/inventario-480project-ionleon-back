<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Sector\ListSectors;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\Sector\ListSectors\ListSectorsQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Sector')]
final class ListSectorsController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/sectors', methods: ['GET'])]
    public function __invoke(): Response
    {
        /** @var ListSectorsResponse $response */
        $response = $this->queryBus->ask(new ListSectorsQuery(
            securityToken: ($this->securityTokenExtractor)(),
        ));

        return new JsonResponse($response->sectors);
    }
}
