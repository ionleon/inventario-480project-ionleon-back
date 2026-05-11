<?php

namespace App\ProjectManagement\Infrastructure\Project;
use App\ProjectManagement\Application\Project\ProjectService;
use App\ProjectManagement\Domain\Project\Project;
use App\ProjectManagement\Domain\Project\ProjectFilters;
use App\ProjectManagement\Domain\Project\ProjectRepositoryInterface;
use App\Service\PaginationService;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/*
 * Cambiar controllers para que utilizen servicios, adaptarlos a arquitectura hexagonal a futuro
 * */

#[Route('/projects')]
#[OA\Tag(name: 'Projects')]
final class ProjectController extends AbstractController
{

    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectService             $projectService,
    )
    {}

    /**
     * @throws Exception
     */
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
    public function index(Request $request): JsonResponse
    {
        $filters = new ProjectFilters(
            term: $request->query->get('term'),
            clientId: $request->query->get('client_id'),
            isActive: $request->query->get('is_active')
        );


        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        $projects = $this->projectRepository->findByFiltersPaginated($filters, $page, $limit);

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
    public function show(Project $project): JsonResponse
    {

        return $this->json($project, 200, [], ['groups' => ['project:read']]);

    }

    /**
     * @throws Exception
     */
    #[Route('',name: 'project_create', methods: ['POST'])]
    public function create( Request $request ): JsonResponse
    {


        $data = json_decode($request->getContent(), true);

        $project = $this->projectService->create($data);

        return $this->json([], 201, [], ['groups' => 'project:read']);



    }

    #[Route('/{id}', name: 'project_edit', methods: ['PUT'])]
    public function edit(Project $project,Request $request) : JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $this->projectService->updateProject($project, $data);

        return $this->json([], 200, [], ['groups' => 'project:read']);

    }

    #[Route('/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(Project $project): JsonResponse
    {

        $this->projectService->deleteProject($project);

        return $this->json(null, 204);
    }

    #[Route('/{id}', name: 'project_deactivate', methods: ['PATCH'])]
    public function deactivate(Project $project, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['is_active'])) {
            return $this->json(['error' => 'Property "is_active" is required'], 400);
        }

        $this->projectService->setProjectActivation($project, (bool) $data['is_active']);

        return $this->json([], 200);
    }
}
