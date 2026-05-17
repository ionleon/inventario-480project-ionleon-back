<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\Project\UpdateProjectDevelopment;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\Project\UpdateProjectDevelopment\UpdateProjectDevelopmentCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project')]
final class UpdateProjectDevelopmentController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {
    }

    #[Route(path: '/projects/{id}/development', methods: ['PATCH'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateProjectDevelopmentRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateProjectDevelopmentCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            status: $request->status,
            notes: $request->notes,
            progress: $request->progress,
        ));

        return new Response(status: Response::HTTP_OK);
    }
}
