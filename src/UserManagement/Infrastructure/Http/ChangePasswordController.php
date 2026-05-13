<?php

namespace App\UserManagement\Infrastructure\Http;

use App\UserManagement\Application\ChangePassword\ChangePasswordCommand;
use App\UserManagement\Application\ChangePassword\ChangePasswordHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}/password-change', name: 'user_password_change', methods: ['PUT'])]
final class ChangePasswordController extends AbstractController
{
    public function __construct(
        private readonly ChangePasswordHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        if ($id !== $this->getUser()?->getUserIdentifier()) {
            throw $this->createAccessDeniedException('You cannot change another user\'s password.');
        }

        $data = json_decode($request->getContent(), true);

        try {
            $this->handler->handle(new ChangePasswordCommand(
                userId:      $id,
                oldPassword: $data['old_password'] ?? '',
                newPassword: $data['new_password'] ?? '',
            ));

            return $this->json(null, 204);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
