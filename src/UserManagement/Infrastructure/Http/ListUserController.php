<?php

namespace App\UserManagement\Infrastructure\Http;

use App\UserManagement\Application\ListUser\ListUserHandler;
use App\UserManagement\Application\ListUser\ListUserQuery;
use App\UserManagement\Application\UserService;

use App\UserManagement\Infrastructure\Http\Response\ListUserResponse;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'User Management')]
#[Route('/users/{id}', name: 'app_user_index', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
class ListUserController extends AbstractController
{
    public function __construct(
       private readonly ListUserHandler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = new ListUserQuery(
            term:       $request->query->get('term'),
            role:       $request->query->get('role'),
            isActive:   $request->query->get('is_active')
                ? $request->query->getBoolean('is_active')
                : null,
            page:       $request->query->get('page'),
            limit:      $request->query->get('limit')
        );

        $users = $this->handler->handle($query);

        return $this->json(ListUserResponse::fromPaginatedResult($users), 200, [], ['groups' => 'user:read']);
    }
}
