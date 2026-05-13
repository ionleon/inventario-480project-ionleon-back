<?php

namespace App\UserManagement\Infrastructure\Http;

use App\UserManagement\Application\ChangePassword\ChangePasswordCommand;
use App\UserManagement\Application\ChangePassword\ChangePasswordHandler;
use App\UserManagement\Application\ResetPassword\ResetPasswordCommand;
use App\UserManagement\Application\ResetPassword\ResetPasswordHandler;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}/admin-password', name: 'user_admin_password_reset', methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
final class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly ResetPasswordHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->handler->handle(new ResetPasswordCommand(
                userId:      $id,
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
