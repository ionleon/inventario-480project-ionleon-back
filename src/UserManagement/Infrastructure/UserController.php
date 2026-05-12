<?php

namespace App\UserManagement\Infrastructure;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\ProjectManagement\Infrastructure\Project\DoctrineProjectRepository;
use App\Service\PaginationService;
use App\UserManagement\Application\UserService;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Domain\AppUserRepositoryInterface;
use App\UserManagement\Domain\UserFilters;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;


/*
 * Cambiar controllers para que utilizen servicios, adaptarlos a arquitectura hexagonal a futuro
 * */


#[OA\Tag(name: 'User Management')]
#[Route('/users')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly AppUserRepositoryInterface $userRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserService $userService
    ) {}

    /**
     * @throws Exception
     */
    #[Route('', name: 'app_user_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Devuelve la lista de usuarios',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: AppUser::class))
        )
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): JsonResponse
    {
        $filters = new UserFilters(
          term:     $request->query->get('term'),
          role:     $request->query->get('role'),
          isActive: $request->query->has('is_active')
                ? $request->query->getBoolean('is_active')
                : null
        );


        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $users = $this->userRepository->findByFiltersPaginated($filters, $page, $limit);


        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(AppUser $user): JsonResponse
    {
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}/projects', name: 'show_projects', methods: ['GET'])]
    public function showProjects(AppUser $user, Request $request) : JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $projects = $this->projectRepository->findByUserPaginated($user->getId(), $page, $limit);

        return $this->json($projects, 200, [], ['groups' => 'project:read']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('',name: 'app_user_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->userService->create($data);
            return $this->json(['message' => 'User created'], 201, [], ['groups' => 'user:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(AppUser $user,Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->userService->update($user, $data);
            return $this->json($user, 200, ['message' => 'User updated'], ['groups' => 'user:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

}

    #[Route('/{id}/password-change', name: 'user_password_change', methods: ['PUT'])]
    public function changePassword(AppUser $user, Request $request ): JsonResponse
    {

        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('No puedes cambiar la contraseña de otro usuario.');
        }

        $data = json_decode($request->getContent(), true);
        $oldPwd = $data['old_password'] ?? '';
        $newPwd = $data['new_password'] ?? '';

        try {
            $this->userService->changePassword($user, $oldPwd, $newPwd);
            return $this->json(['message' => 'Successful password update.'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }


    }

    #[Route('/{id}/admin-password', name: 'user_admin_password_reset', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminPasswordChange(AppUser $user, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newPwd = $data['new_password'] ?? '';

        try {
            $this->userService->resetPassword($user, $newPwd);
            return $this->json(['message' => 'Successful password reset.'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(AppUser $user): JsonResponse
    {
        try {
            $this->userService->delete($user);
            return $this->json(['message' => 'User deleted'], 204);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'app_user_deactivate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleActivation(AppUser $user) : JsonResponse
    {
        try {
            $this->userService->toggleActivation($user);
            return $this->json(['message' => 'User status updated'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

}
