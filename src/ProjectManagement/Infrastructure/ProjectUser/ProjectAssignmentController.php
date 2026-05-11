<?php

namespace App\ProjectManagement\Infrastructure\ProjectUser;

use App\ProjectManagement\Application\ProjectUser\ProjectAssignmentService;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\ProjectUser\ProjectUser;
use App\ProjectManagement\Domain\ProjectUser\ProjectUserRepositoryInterface;
use App\UserManagement\Domain\AppUser;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/users')]
#[OA\Tag(name: 'Project Assignments')]
final class ProjectAssignmentController extends AbstractController
{
    public function __construct(
        private readonly ProjectUserRepositoryInterface $puRepository,
        private readonly ProjectAssignmentService       $assignmentService,
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


        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $assignments = $this->puRepository->findByProjectPaginated($project->getId(), $page, $limit);

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


        try {
            $assignment = $this->assignmentService->assignUser(
                $project,
                $data['user_id'] ?? '',
                $data['role_id'] ?? '');
            return $this->json(['message' => 'User assigned'], 201);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
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
    public function syncUserAssignments(
        Project $project,
        Request $request
    ): JsonResponse {

        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);
        $data = json_decode($request->getContent(), true);

        $this->assignmentService->syncProjectUsers($project, $data['users'] ?? []);

        return $this->json($project->getProjectUsers(), 200, [], ['groups' => 'project:read']);
    }

    /**
     * @throws Exception
     */
    #[Route('/{userId}', name: 'update_single', methods: ['PUT'])]
    public function updateSingle(
        Project $project,
        #[MapEntity(mapping: ['userId' => 'id'])] AppUser $user,
        Request $request
    ): JsonResponse {

        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);
        $data = json_decode($request->getContent(), true);

        $assignment = $this->puRepository->findOneByProjectAndUser($project->getId(), $user->getId());
        if (!$assignment) {
            return $this->json(['error' => 'Assignment not found'], 404);
        }

        $assignment = $this->assignmentService->updateAssignment($assignment, $data);

        return $this->json(['message' => 'Assignment was updated.'], 200);
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

        $assignment = $this->puRepository->findOneByProjectAndUser($project->getId(),$user->getId());
        if (!$assignment) {
            return $this->json(['error' => 'Assignment not found'], 404);
        }

        $this->puRepository->remove($assignment);

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
        #[MapEntity(mapping: ['userId' => 'id'])] AppUser $user,
        Request $request
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_MANAGE_USERS', $project);

        $assignment = $this->puRepository->findOneByProjectAndUser($project->getId(),$user->getId());

        if (!$assignment) {
            return $this->json(['error' => 'Assignment not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $this->assignmentService->deactivateAssignment($assignment, $data);

        return $this->json([], 200);

    }


}
