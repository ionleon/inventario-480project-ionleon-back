<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\ToggleActivation\ToggleActivationCommand;
use App\UserManagement\Application\ToggleActivation\ToggleActivationHandler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}', name: 'app_user_deactivate', methods: ['PATCH'])]
#[IsGranted('ROLE_ADMIN')]
final class ToggleActivationController extends AppController
{
    public function __construct(
        private readonly ToggleActivationHandler $handler,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->handler->handle(new ToggleActivationCommand($id));
            return $this->json(null, 204);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
