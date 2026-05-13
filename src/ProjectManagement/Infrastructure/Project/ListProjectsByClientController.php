<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\ListProjectsByClient\ListProjectsByClientHandler;
use App\ProjectManagement\Application\ListProjectsByClient\ListProjectsByClientQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Projects')]
#[Route('/clients/{id}/projects', name: 'app_client_projects', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class ListProjectsByClientController extends AbstractController
{
    public function __construct(
        private readonly ListProjectsByClientHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $query = new ListProjectsByClientQuery(
            clientId: $id,
            page:     $request->query->getInt('page', 1),
            limit:    $request->query->getInt('limit', 10),
        );

        $projects = $this->handler->handle($query);

        return $this->json($projects, 200, [], ['groups' => ['project:read']]);
    }
}
