<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\AssignUserToProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectUser\AssignUserToProject\AssignUserToProjectCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[OA\Tag(name: 'ProjectUser')]
final class AssignUserToProjectController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/projects/{projectId}/users', methods: ['POST'])]
    public function __invoke(string $projectId, #[MapRequestPayload] AssignUserToProjectRequest $request): Response
    {
        $this->commandBus->dispatch(new AssignUserToProjectCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id ?? Uuid::v4()->toRfc4122(),
            projectId: $projectId,
            userId: $request->userId,
            roleId: $request->roleId,
            allocation: $request->allocation,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
