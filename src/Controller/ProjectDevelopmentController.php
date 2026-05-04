<?php

namespace App\Controller;

use App\Entity\Development;
use App\Entity\Project;
use App\Repository\DevelopmentRepository;
use App\Service\DevelopmentManager;
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
        private DevelopmentManager $devManager,
        private DevelopmentRepository $repository
    ) {}

    #[Route('', name: 'project_development_index', methods: ['GET'])]
    public function index(Project $project): Response
    {
        $developments = $this->devManager->findAllByProject($project);
        return $this->json($developments, 200, [], ['groups' => 'dev:read']);
    }

    #[Route('', name: 'project_development_create', methods: ['POST'])]
    public function create(Project $project, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('PROJECT_EDIT', $project);

        $data = json_decode($request->getContent(), true);
        $dev = $this->devManager->create($project, $data);
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

        if ($development->getProject() !== $project) {
            throw $this->createAccessDeniedException('This development does not belong to this project.');
        }

        $data = json_decode($request->getContent(), true);
        $dev = $this->devManager->save($development, $data);
        return $this->json([], 201, [], ['groups' => 'dev:read']);
    }

    #[Route('/{developmentId}', name: 'project_development_delete', methods: ['DELETE'])]
    public function delete(
        #[MapEntity(mapping: ['id' => 'developmentId'])] Development $development,
    ): JsonResponse
    {
        $project = $development->getProject();
        $this->denyAccessUnlessGranted('PROJECT_EDIT', $project );

        $this->devManager->delete($development);
        return $this->json(null, 204, [], []);

    }

}
