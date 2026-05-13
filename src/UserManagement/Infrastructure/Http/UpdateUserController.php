<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Domain\Enum\SystemRole;
use App\UserManagement\Application\UpdateUser\UpdateUserCommand;
use App\UserManagement\Application\UpdateUser\UpdateUserHandler;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}', name: 'app_user_edit', methods: ['PUT'])]
#[IsGranted('ROLE_ADMIN')]
final class UpdateUserController extends AbstractController
{
    public function __construct(
        private readonly UpdateUserHandler $handler,
    ) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(),true);
        try {
            $command = new UpdateUserCommand(
                userId:   $id,
                name:     $data['name']      ?? null,
                surname:  $data['surname']   ?? null,
                email:    $data['email']     ?? null,
                role:     isset($data['role'])
                    ? SystemRole::from($data['role'])
                    : null,
                isActive: $data['is_active'] ?? null,
            );

            $user = $this->handler->handle($command);

            return $this->json([], 200, [], ['groups' => ['user:read']]);
        } catch (\ValueError $e) {
            return $this->json(['error' => 'Invalid role value.'], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }
}
