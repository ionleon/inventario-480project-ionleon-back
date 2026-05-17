<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\SyncProjectUsers;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectUser\SyncProjectUsers\SyncProjectUsersCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectUser')]
final class SyncProjectUsersController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/projects/{projectId}/users', methods: ['PUT'])]
    public function __invoke(string $projectId, #[MapRequestPayload] SyncProjectUsersRequest $request): Response
    {
        $this->commandBus->dispatch(new SyncProjectUsersCommand(
            securityToken: ($this->securityTokenExtractor)(),
            projectId: $projectId,
            users: $request->users,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
