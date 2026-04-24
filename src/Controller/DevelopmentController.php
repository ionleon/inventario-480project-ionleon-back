<?php

namespace App\Controller;

use App\Entity\Development;
use App\Repository\DevelopmentRepository;
use App\Service\DevelopmentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/developments', name: 'app_development')]
final class DevelopmentController extends AbstractController
{
    public function __construct(
        private DevelopmentManager $manager,
        private DevelopmentRepository $repository
    ) {}

    #[Route('' , name: 'development_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $developments = $this->repository->findAll();
        return $this->json($developments, 200, [], ['groups' => 'dev:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Development $development): JsonResponse
    {
        return $this->json($development, 200, [], ['groups' => 'dev:read']);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $development = $this->manager->create($data);
            return $this->json([], 201, [], );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
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
