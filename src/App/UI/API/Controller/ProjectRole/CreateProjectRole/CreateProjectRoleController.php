<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectRole\CreateProjectRole;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectRole\CreateProjectRole\CreateProjectRoleCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectRole')]
final class CreateProjectRoleController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/project-roles', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateProjectRoleRequest $request): Response
    {
        $this->commandBus->dispatch(new CreateProjectRoleCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $request->id,
            name: $request->name,
        ));

        return new Response(status: Response::HTTP_CREATED);
    }
}
