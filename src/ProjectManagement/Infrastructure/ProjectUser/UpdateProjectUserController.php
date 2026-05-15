<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\UpdateProjectUser\UpdateProjectUserCommand;
use App\ProjectManagement\Application\UpdateProjectUser\UpdateProjectUserHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Assignments')]
// #[Route('/projects/{id}/users/{userId}', name: 'project_users_update_single', methods: ['PUT'])]
final class UpdateProjectUserController extends AbstractController
{
    public function __construct(
        private readonly UpdateProjectUserHandler $handler,
    ) {}

    public function __invoke(string $id, string $userId, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS');

        $data = json_decode($request->getContent(), true);

        try {
            $command = new UpdateProjectUserCommand(
                projectId: $id,
                userId:    $userId,
                roleId:    $data['role_id'] ?? null,
                isActive:  isset($data['is_active']) ? (bool) $data['is_active'] : null,
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Assignment was updated.'], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
