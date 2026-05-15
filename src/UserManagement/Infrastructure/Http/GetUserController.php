<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\GetUser\GetUserHandler;
use App\UserManagement\Application\GetUser\GetUserQuery;
use App\UserManagement\Infrastructure\Http\Response\GetUserResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'User Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/User/
// #[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
final class GetUserController extends AppController
{
    public function __construct(
        private readonly GetUserHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $user = $this->handler->handle(new GetUserQuery($id));
            return $this->json(GetUserResponse::fromEntity($user), 200);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
