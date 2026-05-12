<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Repository\AppUserRepository;
use App\Repository\ProjectRoleRepository;
use App\Repository\ProjectUserRepository;
use App\Service\PaginationService;
use App\Service\ProjectAssignmentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/projects/{id}/users')]
#[OA\Tag(name: 'Project Assignments')]
final class ProjectAssignmentController extends AbstractController
{
    public function __construct(
        private readonly ProjectUserRepository    $puRepository,
        private readonly AppUserRepository        $userRepository,
        private readonly ProjectRoleRepository    $roleRepository,
        private readonly ProjectAssignmentManager $assignmentManager,
        private readonly PaginationService        $paginationService
    )
    {}

    /**
     * @throws Exception
     */
    #[Route('', name: 'project_users_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retorna la lista de usuarios asignados al proyecto.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ProjectUser::class, groups: ['project:read']))
        )
    )]
    public function index(Project $project, Request $request): JsonResponse
    {
        $qb = $this->puRepository->qbAllByProjects($project);

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $assignments = $this->paginationService->paginate($qb, $page, $limit);
        return $this->json($assignments, 200, [], ['groups' => 'project:read']);
    }


    #[Route('', name: 'project_users_add_user', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Datos para asignar un usuario',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'user_id', type: 'string', example: 'uuid-usuario'),
                new OA\Property(property: 'role_id', type: 'integer', example: 1)
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Usuario asignado con éxito')]
    #[OA\Response(response: 400, description: 'Datos inválidos')]
    public function addUser(
        Project $project,
        Request $request
    ): JsonResponse {

        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);

        $data = json_decode($request->getContent(), true);

        $user = $this->userRepository->find($data['user_id'] ?? '');
        $role = $this->roleRepository->find($data['role_id'] ?? '');

        if (!$user || !$role) {
            return $this->json(['error' => 'User or Role not found'], 404);
        }

        try {
            $assignment = $this->assignmentManager->assignUser($project, $user, [
                'role_id' => $role->getId(),
            ]);
            return $this->json([], 201, [], ['groups' => 'project:read']);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

    }

    #[Route('', name: 'update', methods: ['PUT'])]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'users',
                    type: 'array',
                    items: new OA\Items(type: 'object') // Puedes detallar el objeto aquí
                )
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Usuarios sincronizados')]
    #[OA\Response(response: 403, description: 'Solo administradores o project managers')]
    public function update(
        Project $project,
        Request $request
    ): JsonResponse {

        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);
        $data = json_decode($request->getContent(), true);

        $this->assignmentManager->syncProjectUsers($project, $data['users'] ?? []);

        return $this->json($project->getProjectUsers(), 200, [], ['groups' => 'project:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/{userId}', name: 'update_single', methods: ['PUT'])]
    public function updateSingle(
        Project $project,
        #[MapEntity(mapping: ['id' => 'userId'])] AppUser $user,
        Request $request
    ): JsonResponse {

        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);
        $data = json_decode($request->getContent(), true);

        $assignment = $this->puRepository->findOneByProjectAndUser($project, $user);

        $this->assignmentManager->updateAssignment($assignment, $data);

        return $this->json($project->getProjectUsers(), 200, [], ['groups' => 'project:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/{userId}', name: 'project_users_remove_user', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Usuario eliminado del proyecto')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID del Proyecto')]
    #[OA\Parameter(name: 'userId', in: 'path', description: 'ID del Usuario a quitar')]
    public function removeUser(
        Project $project,
        #[MapEntity(mapping: ['userId' => 'id'])] AppUser $user
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);

        $assignment = $this->assignmentManager->findAssignment($project,$user);
        $this->assignmentManager->removeAssignment($assignment);

        return $this->json(null, 204);

    }

    /**
     * @throws Exception
     */
    #[Route('/{userId}', name: 'project_users_deactivate_user', methods: ['PATCH'])]
    #[OA\Response(response: 204, description: 'Usuario desactivado del proyecto')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID del Proyecto')]
    #[OA\Parameter(name: 'userId', in: 'path', description: 'ID del Usuario a desactivar')]
    public function deactivateUserAssignment(
        Project $project,
        #[MapEntity(mapping: ['userId' => 'id'])] AppUser $user
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);

        $assignment = $this->assignmentManager->findAssignment($project,$user);
        $this->assignmentManager->deactivateAssignment($assignment);

        return $this->json([], 200);

    }


}
