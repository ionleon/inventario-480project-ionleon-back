<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\SyncProjectUsers\SyncProjectUsersCommand;
use App\ProjectManagement\Application\SyncProjectUsers\SyncProjectUsersHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Assignments')]
// #[Route('/projects/{id}/users', name: 'project_users_sync', methods: ['PUT'])]
final class SyncProjectUsersController extends AbstractController
{
    public function __construct(
        private readonly SyncProjectUsersHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS');

        $data = json_decode($request->getContent(), true);

        try {
            $command = new SyncProjectUsersCommand(
                projectId: $id,
                users:     $data['users'] ?? [],
            );

            $this->handler->handle($command);

            return $this->json(['message' => 'Users synchronized'], 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
