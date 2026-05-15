<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\AssignUserToProject\AssignUserToProjectCommand;
use App\ProjectManagement\Application\AssignUserToProject\AssignUserToProjectHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Assignments')]
#[Route('/projects/{id}/users', name: 'project_users_add_user', methods: ['POST'])]
final class AssignUserToProjectController extends AbstractController
{
    public function __construct(
        private readonly AssignUserToProjectHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS');

        $data = json_decode($request->getContent(), true);

        try {
            $command = new AssignUserToProjectCommand(
                projectId: $id,
                userId:    $data['user_id'] ?? throw new \InvalidArgumentException('user_id is required.'),
                roleId:    $data['role_id'] ?? throw new \InvalidArgumentException('role_id is required.'),
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'User assigned'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400);
        }
    }
}
