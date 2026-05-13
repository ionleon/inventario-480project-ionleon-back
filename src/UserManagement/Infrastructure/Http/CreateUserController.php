<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Infrastructure\Http\AppController;
use App\UserManagement\Application\CreateUser\CreateUserCommand;
use App\UserManagement\Application\CreateUser\CreateUserHandler;
use App\UserManagement\Infrastructure\Http\Request\CreateUserRequest;
use App\UserManagement\Infrastructure\Http\Response\GetUserResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'User Management')]
#[Route('/users', name: 'app_user_create', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
final class CreateUserController extends AppController
{
    public function __construct(
        private readonly CreateUserHandler $handler,
        private readonly ValidatorInterface $validator,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $dto = CreateUserRequest::fromRequest($request);

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->json(['errors' => $this->formatViolations($violations)], 400);
        }

        try {
            $command = new CreateUserCommand(
                id:       $dto->id,
                email:    $dto->email,
                password: $dto->password,
                name:     $dto->name,
                surname:  $dto->surname,
                role:     $dto->role,
            );

            $user = $this->handler->handle($command);

            return $this->json(GetUserResponse::fromEntity($user), 201);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }
}
