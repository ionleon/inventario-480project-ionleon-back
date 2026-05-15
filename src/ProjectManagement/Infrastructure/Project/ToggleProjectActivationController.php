<?php

namespace App\ProjectManagement\Infrastructure\Project;

use App\ProjectManagement\Application\ToggleProjectActivation\ToggleProjectActivationCommand;
use App\ProjectManagement\Application\ToggleProjectActivation\ToggleProjectActivationHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/Project/
// #[Route('/projects/{id}', name: 'project_deactivate', methods: ['PATCH'])]
final class ToggleProjectActivationController extends AbstractController
{
    public function __construct(
        private readonly ToggleProjectActivationHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new ToggleProjectActivationCommand($id));

            return $this->json([], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
