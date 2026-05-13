<?php

namespace App\UserManagement\Infrastructure\Http;

use App\UserManagement\Application\GetUser\GetUserHandler;
use App\UserManagement\Application\GetUser\GetUserQuery;

use App\UserManagement\Infrastructure\Http\Response\GetUserResponse;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
class GetUserController extends AbstractController
{
    public function __construct(
      private readonly GetUserHandler $handler
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try{
            $user = $this->handler->handle(new GetUserQuery($id));
            return $this->json(GetUserResponse::fromEntity($user), 200, [], ['groups' => 'user:read']);
        } catch (\DomainException $exception) {
            return $this->json(['error' => $exception->getMessage()], 404, [], ['groups' => 'user:read']);
        }
    }


}
