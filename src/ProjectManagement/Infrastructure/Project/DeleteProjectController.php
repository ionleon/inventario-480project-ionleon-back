<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\DeleteProject\DeleteProjectCommand;
use App\ProjectManagement\Application\DeleteProject\DeleteProjectHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Project/
// #[Route('/projects/{id}', name: 'project_delete', methods: ['DELETE'])]
final class DeleteProjectController extends AbstractController
{
    public function __construct(
        private readonly DeleteProjectHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteProjectCommand($id));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
