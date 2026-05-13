<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\RemoveUserFromProject\RemoveUserFromProjectCommand;
use App\ProjectManagement\Application\RemoveUserFromProject\RemoveUserFromProjectHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Assignments')]
#[Route('/projects/{id}/users/{userId}', name: 'project_users_remove_user', methods: ['DELETE'])]
final class RemoveUserFromProjectController extends AbstractController
{
    public function __construct(
        private readonly RemoveUserFromProjectHandler $handler,
    ) {}

    public function __invoke(string $id, string $userId): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS');

        try {
            $this->handler->handle(new RemoveUserFromProjectCommand($id, $userId));

            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
