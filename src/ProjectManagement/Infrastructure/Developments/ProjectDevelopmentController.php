<?php

namespace App\ProjectManagement\Infrastructure\Developments;

use App\ProjectManagement\Application\Developments\DevelopmentService;
use App\ProjectManagement\Domain\Developments\Development;
use App\ProjectManagement\Domain\Developments\DevelopmentRepositoryInterface;
use App\ProjectManagement\Domain\Project\Project;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/developments', name: 'app_project_development')]
final class ProjectDevelopmentController extends AbstractController
{
    public function __construct(
        private DevelopmentService             $devService,
        private DevelopmentRepositoryInterface $repository
    ) {}

    #[Route('', name: 'project_development_index', methods: ['GET'])]
    public function index(Project $project): Response
    {
        $developments = $this->repository->findByProject($project);
        return $this->json($developments, 200, [], ['groups' => 'dev:read']);
    }

    #[Route('', name: 'project_development_create', methods: ['POST'])]
    public function create(Project $project, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_EDIT', $project);

        try {
            $data = json_decode($request->getContent(), true);
            $dev = $this->devService->create($project, $data);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
        return $this->json([], 201, [], ['groups' => 'dev:read']);
    }

    #[Route('/{developmentId}', name: 'project_development_update', methods: ['PUT'])]
    public function update(
        Project $project,
        #[MapEntity(mapping: ['id' => 'developmentId'])] Development $development,
        Request $request
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_EDIT', $project);


        try {
            $data = json_decode($request->getContent(), true);
            $this->devService->update($development, $data);
            return $this->json([], 201, [], ['groups' => 'dev:read']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 403);
        }
    }

    #[Route('/{developmentId}', name: 'project_development_delete', methods: ['DELETE'])]
    public function delete(
        #[MapEntity(mapping: ['id' => 'developmentId'])] Development $development,
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_EDIT', $development->getProject() );

        $this->devService->delete($development);
        return $this->json(null, 204, [], []);

    }

}
