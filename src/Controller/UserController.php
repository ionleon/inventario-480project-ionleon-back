<?php

namespace App\Controller;
use App\ProjectManagement\Infrastructure\Project\DoctrineProjectRepository;
use App\Service\PaginationService;
use App\Service\UserManager;
use App\UserManagement\Domain\AppUser;
use App\UserManagement\Infrastructure\DoctrineUserRepository;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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
        private readonly DoctrineUserRepository    $userRepository,
        private readonly DoctrineProjectRepository $projectRepository,
        private readonly UserManager               $userManager,
        private readonly PaginationService         $paginationService,
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
        $term = $request->query->get('term');
        $role = $request->query->get('role');
        $isActive = $request->query->has('is_active')
                    ? $request->query->getBoolean('is_active')
                    : null;

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $qb = $this->userRepository->qbByFilters($term, $role, $isActive);
        $users = $this->paginationService->paginate($qb, $page, $limit);


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
        $qb = $this->projectRepository->findByUser($user);

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $projects = $this->paginationService->paginate($qb);

        return $this->json($projects, 200, [], ['groups' => 'project:read']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('',name: 'app_user_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $user = $this->userManager->create($data);

        return $this->json([], 201, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(AppUser $user,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userManager->save($user, $data);

        return $this->json($user, 200, [], ['groups' => 'user:read']);

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
            $this->userManager->changePassword($user, $oldPwd, $newPwd);
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
            $this->userManager->resetPassword($user, $newPwd);
            return $this->json(['message' => 'Successful password reset.'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(AppUser $user): JsonResponse
    {
        $this->userManager->remove($user);

        return $this->json(null, 204);
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}', name: 'app_user_deactivate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deactivate(AppUser $user) : JsonResponse
    {
        $this->userManager->deactivateUser($user);

        return $this->json([], 200);
    }

}
