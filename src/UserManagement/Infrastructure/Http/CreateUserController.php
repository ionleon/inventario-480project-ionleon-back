<?php

namespace App\UserManagement\Infrastructure\Http;

use App\Shared\Domain\Enum\SystemRole;
use App\UserManagement\Application\CreateUser\CreateUserCommand;
use App\UserManagement\Application\CreateUser\CreateUserHandler;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users', name: 'app_user_create', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
class CreateUserController extends AbstractController
{
    public function __construct(
        private readonly CreateUserHandler $handler,
    ) {}

    public function __invoke(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent, true);

        try{
            $command = new CreateUserCommand(
                id:         $data['id']             ?? throw new \InvalidArgumentException('Id is required.'),
                email:      $data['email']          ?? throw new \InvalidArgumentException('Email is required.'),
                password:   $data['password']       ?? throw new \InvalidArgumentException('Password is required.'),
                name:       $data['name']           ?? '',
                surname:    $data['surname']        ?? '',
                role:       SystemRole::from($data['role'] ?? 'ROLE_USER'),
            );

            $user = $this->handler->handle($command);

            return $this->json([], 201, [], ['groups' => ['user:read']]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }

    }

}
