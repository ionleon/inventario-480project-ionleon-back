<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Application\UpdateProjectRole\UpdateProjectRoleCommand;
use App\ProjectManagement\Application\UpdateProjectRole\UpdateProjectRoleHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Roles')]
// MIGRATED — route disabled pending new UpdateProjectRole controller in Core slice
// #[Route('/project-roles/{id}', name: 'project_role_update', methods: ['PUT'])]
final class UpdateProjectRoleController extends AbstractController
{
    public function __construct(
        private readonly UpdateProjectRoleHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateProjectRoleCommand(
                roleId: $id,
                name:   $data['name'] ?? null,
            );

            $this->handler->handle($command);

            return $this->json([], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
