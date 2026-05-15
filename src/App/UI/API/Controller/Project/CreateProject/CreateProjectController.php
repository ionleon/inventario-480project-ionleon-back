<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\CreateProject;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Project\CreateProject\CreateProjectCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project')]
final class CreateProjectController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/projects', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateProjectRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateProjectCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            name: $request->name,
            description: $request->description,
            clientId: $request->clientId,
            managerId: $request->managerId,
            technologyIds: $request->technologyIds,
            startDate: $request->startDate,
            endDate: $request->endDate,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
