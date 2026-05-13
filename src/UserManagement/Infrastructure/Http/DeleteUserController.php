<?php

namespace App\UserManagement\Infrastructure\Http;

use App\UserManagement\Application\DeleteUser\DeleteUserHandler;
use App\UserManagement\Domain\AppUser;
use OpenApi\Attributes as OA;
use App\UserManagement\Application\DeleteUser\DeleteUserCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}', name: 'app_user_delete', methods: ['DELETE'])]
#[IsGranted('ROLE_ADMIN')]
class DeleteUserController extends AbstractController
{
    public function __construct(
      private readonly DeleteUserHandler $handler
    ) {}

    public function __invoke(AppUser $user): JsonResponse
    {
        try {
            $this->handler->handle(new DeleteUserCommand($user->getId()));
            return $this->json(null, 204);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
