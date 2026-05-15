<?php

declare(strict_types=1);

namespace App\App\UI\API\Controller\ProjectRole\DeleteProjectRole;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use App\Core\Application\Bus\CommandBus;
use App\Core\Application\Command\ProjectRole\DeleteProjectRole\DeleteProjectRoleCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'ProjectRole')]
final class DeleteProjectRoleController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly SecurityTokenExtractorInterface $securityTokenExtractor,
    ) {}

    #[Route(path: '/project-roles/{id}', methods: ['DELETE'])]
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteProjectRoleCommand(
            securityToken: ($this->securityTokenExtractor)(),
            id: $id,
        ));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
