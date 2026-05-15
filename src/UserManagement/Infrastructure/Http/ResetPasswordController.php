<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\ResetPassword\ResetPasswordCommand;
use App\UserManagement\Application\ResetPassword\ResetPasswordHandler;
use App\UserManagement\Infrastructure\Http\Request\ResetPasswordRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'User Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/User/
// #[Route('/users/{id}/admin-password', name: 'user_admin_password_reset', methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
final class ResetPasswordController extends AppController
{
    public function __construct(
        private readonly ResetPasswordHandler $handler,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $dto = ResetPasswordRequest::fromRequest($request);

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->json(['errors' => $this->formatViolations($violations)], 400);
        }

        try {
            $this->handler->handle(new ResetPasswordCommand(
                userId:      $id,
                newPassword: $dto->newPassword,
            ));

            return $this->json(null, 204);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
