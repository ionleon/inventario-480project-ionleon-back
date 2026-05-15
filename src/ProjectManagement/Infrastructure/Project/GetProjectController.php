<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\GetProject\GetProjectHandler;
use App\ProjectManagement\Application\GetProject\GetProjectQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Project/
// #[Route('/projects/{id}', name: 'project_detail_show', methods: ['GET'])]
final class GetProjectController extends AbstractController
{
    public function __construct(
        private readonly GetProjectHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $project = $this->handler->handle(new GetProjectQuery($id));

            return $this->json($project, 200, [], ['groups' => ['project:read']]);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
