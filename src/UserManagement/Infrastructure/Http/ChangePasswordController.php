<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\ChangePassword\ChangePasswordCommand;
use App\UserManagement\Application\ChangePassword\ChangePasswordHandler;
use App\UserManagement\Infrastructure\Http\Request\ChangePasswordRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'User Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/User/
// #[Route('/users/{id}/password-change', name: 'user_password_change', methods: ['PUT'])]
final class ChangePasswordController extends AppController
{
    public function __construct(
        private readonly ChangePasswordHandler $handler,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        if ($id !== $this->getUser()?->getUserIdentifier()) {
            throw $this->createAccessDeniedException('You cannot change another user\'s password.');
        }

        $dto = ChangePasswordRequest::fromRequest($request);

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->json(['errors' => $this->formatViolations($violations)], 400);
        }

        try {
            $this->handler->handle(new ChangePasswordCommand(
                userId:      $id,
                oldPassword: $dto->oldPassword,
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
