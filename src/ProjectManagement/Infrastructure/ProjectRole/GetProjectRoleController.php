<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Application\GetProjectRole\GetProjectRoleHandler;
use App\ProjectManagement\Application\GetProjectRole\GetProjectRoleQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Roles')]
// MIGRATED — route disabled pending new GetProjectRole controller in Core slice
// #[Route('/project-roles/{id}', name: 'project_role_show', methods: ['GET'])]
final class GetProjectRoleController extends AbstractController
{
    public function __construct(
        private readonly GetProjectRoleHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $role = $this->handler->handle(new GetProjectRoleQuery($id));

            return $this->json($role, 200, [], ['groups' => ['project_role:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
