<?php

namespace App\Controller;

use App\Entity\Development;
use App\ProjectManagement\Infrastructure\Project\DoctrineProjectRepository;
use App\Repository\DevelopmentRepository;
use App\Service\DevelopmentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/developments', name: 'app_development')]
final class DevelopmentController extends AbstractController
{
    public function __construct(
        private DevelopmentManager $manager,
        private DevelopmentRepository $devRepository,
        private DoctrineProjectRepository $projectRepository
    ) {}

    #[Route('' , name: 'development_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $developments = $this->devRepository->findAll();
        return $this->json($developments, 200, [], ['groups' => 'dev:read']);
    }

    #[Route('/{id}', name: 'development_show', methods: ['GET'])]
    public function show(Development $development): JsonResponse
    {
        return $this->json($development, 200, [], ['groups' => 'dev:read']);
    }
    #[Route('/{id}/links', name: 'show_links', methods: ['GET'])]
    public function getDevelopmentLinks(Development $development): JsonResponse
    {
        $links = $development->getLinks();

        return $this->json($links, 200, [], ['groups' => 'link:read']);
    }

    #[Route('', name: 'development_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['projectId'])) {
            throw new \InvalidArgumentException('Faltan campos obligatorios (projectId).');
        }
        $project = $this->projectRepository->find($data['projectId']);
        try {
            $development = $this->manager->create($project, $data);
            return $this->json([], 201, [], );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'development_update', methods: ['PUT', 'PATCH'])]
    public function update(Development $development, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->manager->save($development, $data);
            return $this->json([], 200, []);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'development_delete', methods: ['DELETE'])]
    public function delete(Development $development): JsonResponse
    {
        $this->manager->delete($development);
        return $this->json(null, 204);
    }


}
