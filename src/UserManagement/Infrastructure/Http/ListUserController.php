<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\ListUser\ListUserHandler;
use App\UserManagement\Application\ListUser\ListUserQuery;
use App\UserManagement\Infrastructure\Http\Response\ListUserResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/User/
// #[Route('/users', name: 'app_user_index', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class ListUserController extends AppController
{
    public function __construct(
        private readonly ListUserHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = new ListUserQuery(
            term:     $request->query->get('term'),
            role:     $request->query->get('role'),
            isActive: $request->query->get('is_active')
                ? $request->query->getBoolean('is_active')
                : null,
            page:     $request->query->getInt('page', 1),
            limit:    $request->query->getInt('limit', 10),
        );

        $users = $this->handler->handle($query);

        return $this->json(ListUserResponse::fromPaginatedResult($users), 200);
    }
}
