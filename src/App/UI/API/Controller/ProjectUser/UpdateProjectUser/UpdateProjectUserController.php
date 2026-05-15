<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectUser\UpdateProjectUser;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectUser\UpdateProjectUser\UpdateProjectUserCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectUser')]
final class UpdateProjectUserController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/project-users/{id}', methods: ['PATCH'])]
    public function __invoke(string $id, #[MapRequestPayload] UpdateProjectUserRequest $request): Response
    {
        $this->commandBus->dispatch(new UpdateProjectUserCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
            roleId: $request->roleId,
            allocation: $request->allocation,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
