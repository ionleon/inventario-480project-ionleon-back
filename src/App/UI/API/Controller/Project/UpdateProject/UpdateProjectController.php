<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\UpdateProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Project\UpdateProject\UpdateProjectCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project')]
final class UpdateProjectController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/projects/{id}', methods: ['PATCH'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateProjectRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateProjectCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            name: $request->name,
            description: $request->description,
            clientId: $request->clientId,
            managerId: $request->managerId,
            technologyIds: $request->technologyIds,
            startDate: $request->startDate,
            endDate: $request->endDate,
            isActive: $request->isActive,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
