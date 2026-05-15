<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Application\DeleteProjectRole\DeleteProjectRoleCommand;
use App\ProjectManagement\Application\DeleteProjectRole\DeleteProjectRoleHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Roles')]
// MIGRATED to App\App\UI\API\Controller\ProjectRole\DeleteProjectRole\DeleteProjectRoleController
// #[Route('/project-roles/{id}', name: 'project_role_delete', methods: ['DELETE'])]
final class DeleteProjectRoleController extends AbstractController
{
    public function __construct(
        private readonly DeleteProjectRoleHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteProjectRoleCommand($id));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
