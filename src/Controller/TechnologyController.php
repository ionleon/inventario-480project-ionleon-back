<?php

namespace App\Controller;

use App\Repository\TechnologyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/technologies', name: 'app_technology')]
final class TechnologyController extends AbstractController
{

    public function __construct(
        private TechnologyManager $manager,
        private TechnologyRepository $repository
    ) {}

    #[Route('', name: 'technologies_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $technologies = $this->repository->findAll();
        return $this->json($technologies, 200, [], ['groups' => ['tech:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Technology $technology): JsonResponse
    {
        return $this->json($technology, 200, [], ['groups' => 'tech:read']);
    }

    #[Route('', name: 'technologies_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $this->manager->create($data);
            return $this->json([], 200, [], []);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
