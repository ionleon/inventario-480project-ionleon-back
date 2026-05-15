<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectRole\ListProjectRoles;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\ProjectRole\ListProjectRoles\ListProjectRolesQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectRole')]
final class ListProjectRolesController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/project-roles', methods: ['GET'])]
    public function __invoke(): Response
    {
        /** @var ListProjectRolesResponse $response */
        $response = $this->queryBus->ask(new ListProjectRolesQuery(
            securityToken: ($this->securityTokenExtractor)(),
        ));

        return new JsonResponse($response->projectRoles);
    }
}
