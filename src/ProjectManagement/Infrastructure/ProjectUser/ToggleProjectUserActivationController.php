<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\ToggleProjectUserActivation\ToggleProjectUserActivationCommand;
use App\ProjectManagement\Application\ToggleProjectUserActivation\ToggleProjectUserActivationHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Assignments')]
// #[Route('/projects/{id}/users/{userId}', name: 'project_users_deactivate_user', methods: ['PATCH'])]
final class ToggleProjectUserActivationController extends AbstractController
{
    public function __construct(
        private readonly ToggleProjectUserActivationHandler $handler,
    ) {}

    public function __invoke(string $id, string $userId): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS');

        try {
            $this->handler->handle(new ToggleProjectUserActivationCommand($id, $userId));

            return $this->json([], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
