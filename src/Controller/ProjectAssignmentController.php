<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Repository\ProjectUserRepository;
use App\Service\ProjectAssignmentManager;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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
    #[Route('', name: 'project_users_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retorna la lista de usuarios asignados al proyecto.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ProjectUser::class, groups: ['project:read']))
        )
    )]
    public function index(Project $project): JsonResponse
    {
        return $this->json($project->getProjectUsers(), 200, [], ['groups' => 'project:read']);
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
        Request $request,
        ProjectAssignmentManager $assignmentManager
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $userId = $data['user_id'];
        $roleId = $data['role_id'];

        if(!$userId || !$roleId) {
            return $this->json(['error' => 'user_id and role_id are required'], 400);
        }

        try {
            $assignment = $assignmentManager->assignUser($project, $userId, $roleId);
            return $this->json($assignment, 201, [], ['groups' => 'project:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }

    }

    #[Route('', name: 'project_users_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
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
    #[OA\Response(response: 403, description: 'Solo administradores')]
    public function update(
        Project $project,
        Request $request,
        ProjectAssignmentManager $assignmentManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $assignmentManager->syncProjectUsers($project, $data['users'] ?? []);

        return $this->json($project->getProjectUsers(), 200, [], ['groups' => 'project:read']);
    }
    #[Route('/{userId}', name: 'project_users_remove_user', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Usuario eliminado del proyecto')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID del Proyecto')]
    #[OA\Parameter(name: 'userId', in: 'path', description: 'ID del Usuario a quitar')]
    public function removeUser(
        Project $project,
        Uuid $userId,
        ProjectUserRepository $puRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $assignment = $puRepository->findOneBy([
            'project' => $project,
            'appUser' => $userId
        ]);

        if ($assignment) {
            $em->remove($assignment);
            $em->flush();
        }

        return $this->json(null, 204);
    }
}
