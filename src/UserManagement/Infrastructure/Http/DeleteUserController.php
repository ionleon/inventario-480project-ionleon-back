<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\DeleteUser\DeleteUserCommand;
use App\UserManagement\Application\DeleteUser\DeleteUserHandler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/User/
// #[Route('/users/{id}', name: 'app_user_delete', methods: ['DELETE'])]
#[IsGranted('ROLE_ADMIN')]
final class DeleteUserController extends AppController
{
    public function __construct(
        private readonly DeleteUserHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteUserCommand($id));
            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
