<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\UpdateUser\UpdateUserCommand;
use App\UserManagement\Application\UpdateUser\UpdateUserHandler;
use App\UserManagement\Infrastructure\Http\Request\UpdateUserRequest;
use App\UserManagement\Infrastructure\Http\Response\GetUserResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'User Management')]
// MIGRATED: route disabled, see src/App/UI/API/Controller/User/
// #[Route('/users/{id}', name: 'app_user_edit', methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
final class UpdateUserController extends AppController
{
    public function __construct(
        private readonly UpdateUserHandler $handler,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $dto = UpdateUserRequest::fromRequest($request);

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->json(['errors' => $this->formatViolations($violations)], 400);
        }

        try {
            $command = new UpdateUserCommand(
                userId:   $id,
                name:     $dto->name,
                surname:  $dto->surname,
                email:    $dto->email,
                role:     $dto->role,
                isActive: $dto->isActive,
            );

            $user = $this->handler->handle($command);

            return $this->json(GetUserResponse::fromEntity($user), 200);
        } catch (\ValueError $e) {
            return $this->json(['error' => 'Invalid role value.'], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }
}
