<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\ListProjectUsers;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\QueryBus;
use App\Core\Application\Query\ProjectUser\ListProjectUsers\ListProjectUsersQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectUser')]
final class ListProjectUsersController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/projects/{projectId}/users', methods: ['GET'])]
    public function __invoke(string $projectId): Response
    {
        /** @var list<ProjectUserResponse> $projectUsers */
        $projectUsers = $this->queryBus->ask(new ListProjectUsersQuery(
            securityToken: ($this->securityTokenExtractor)(),
            projectId: $projectId,
        ));

        return new JsonResponse($projectUsers);
    }
}
