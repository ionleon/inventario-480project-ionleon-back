<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\ListProjects\ListProjectsHandler;
use App\ProjectManagement\Application\ListProjects\ListProjectsQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Project/
// #[Route('/projects', name: 'project_index', methods: ['GET'])]
final class ListProjectsController extends AbstractController
{
    public function __construct(
        private readonly ListProjectsHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = new ListProjectsQuery(
            term:     $request->query->get('term'),
            clientId: $request->query->get('client_id'),
            isActive: $request->query->has('is_active')
                ? $request->query->getBoolean('is_active')
                : null,
            page:     $request->query->getInt('page', 1),
            limit:    $request->query->getInt('limit', 10),
        );

        $projects = $this->handler->handle($query);

        return $this->json($projects, 200, [], ['groups' => ['project:read']]);
    }
}
