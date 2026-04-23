<?php

namespace App\Controller;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Service\ProjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/*
 * Cambiar controllers para que utilizen servicios, adaptarlos a arquitectura hexagonal a futuro
 * */

#[Route('/projects')]
#[OA\Tag(name: 'Projects')]
final class ProjectController extends AbstractController
{


    #[Route('', name: 'project_index', methods: ['GET'])]
    #[OA\Get(
        summary: 'Listar todos los proyectos',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de proyectos obtenida correctamente',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Project::class, groups: ['project:read']))
                )
            )
        ]
    )]
    public function index(ProjectRepository $repository): JsonResponse
    {

        $projects = $repository->findAll();

        return $this->json($projects, 200, [], ['groups' => ['project:read']]);
    }

    #[Route('/{id}', name: 'project_detail_show', methods: ['GET'])]
    #[OA\Get(
        path: '/projects/{id}',
        summary: 'Obtiene el detalle de un proyecto',
        tags: ['Projects'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Proyecto encontrado',
                content: new OA\JsonContent(ref: new Model(type: Project::class, groups: ['project:read']))
            ),
            new OA\Response(response: 404, description: 'Proyecto no encontrado')
        ]
    )]
    public function show(Uuid $id, ProjectRepository $repository): JsonResponse
    {
        $project = $repository -> find($id);

        if (!$project) {
            return $this->json(['error' => 'Project not found'], status: 404);
        }

        return $this->json($project, 200, [], ['groups' => ['project:read']]);
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('',name: 'project_create', methods: ['POST'])]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ProjectManager $projectManager
    ):JsonResponse
    {
        try {
            $project = $serializer->deserialize($request->getContent(), Project::class, 'json', [
                'groups' => ['project:write'],
            ]);

            $data = json_decode($request->getContent(), true);

            $projectManager->create($project, $data['client_id'] ?? null);

            return $this->json($project, 201, [], ['groups' => 'project:read']);
        } catch(\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                $e->getCode() ?: 500
            );
        }

    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'project_edit', methods: ['PUT'])]
    public function edit(
        Uuid $id,
        ProjectRepository $repository,
        Request $request,
        SerializerInterface $serializer,
        ProjectManager $projectManager,
        ValidatorInterface $validator
    ):JsonResponse
    {
        $project = $repository -> find($id);

        if (!$project) {
            return $this->json(['error' => 'Project not found'], 404);
        }

        $serializer->deserialize(
            $request->getContent(),
            Project::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $project,
                'groups' => ['project:write']
            ]
        );

        $errors = $validator->validate($project);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $projectManager->update($project);

        return $this->json($project, 200, [], ['groups' => 'project:read']);

    }

    #[Route('/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(Uuid $id, ProjectRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $project = $repository->find($id);
        if (!$project) return $this->json(['error' => 'Project not found'], 404);

        $em->remove($project);
        $em->flush();

        return $this->json(null, 204);
    }

    #[Route('/{id}', name: 'project_deactivate', methods: ['PATCH'])]
    public function deactivate(
        Uuid $id,
        ProjectRepository $repository,
        ProjectManager$projectManager
    ): JsonResponse
    {

        $project = $repository -> find($id);

        if(!$project){
            return $this->json(['error' => 'Project not found'], 404);
        }

        $projectManager->deactivate($project);

        return $this->json([
            'message' => 'Project has been deactivated',
            'id' => $project->getId()
        ], 200);
    }
}
