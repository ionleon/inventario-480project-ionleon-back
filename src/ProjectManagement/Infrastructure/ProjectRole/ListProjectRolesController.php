<?php

namespace App\ProjectManagement\Infrastructure\ProjectRole;

use App\ProjectManagement\Application\ListProjectRoles\ListProjectRolesHandler;
use App\ProjectManagement\Application\ListProjectRoles\ListProjectRolesQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Roles')]
#[Route('/project-roles', name: 'project_role_index', methods: ['GET'])]
final class ListProjectRolesController extends AbstractController
{
    public function __construct(
        private readonly ListProjectRolesHandler $handler,
    ) {}

    public function __invoke(): JsonResponse
    {
        $roles = $this->handler->handle(new ListProjectRolesQuery());

        return $this->json($roles, 200, [], ['groups' => ['project_role:read']]);
    }
}
