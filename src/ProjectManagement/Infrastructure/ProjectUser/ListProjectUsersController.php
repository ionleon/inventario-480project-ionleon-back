<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\ListProjectUsers\ListProjectUsersHandler;
use App\ProjectManagement\Application\ListProjectUsers\ListProjectUsersQuery;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Project Assignments')]
// #[Route('/projects/{id}/users', name: 'project_users_index', methods: ['GET'])]
final class ListProjectUsersController extends AbstractController
{
    public function __construct(
        private readonly ListProjectUsersHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $users = $this->handler->handle(new ListProjectUsersQuery($id, $page, $limit));

        return $this->json($users, 200);
    }
}
