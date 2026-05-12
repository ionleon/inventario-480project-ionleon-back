<?php

namespace App\ClientManagement\Infrastructure\Sector;

use App\ClientManagement\Application\Sector\SectorService;
use App\ClientManagement\Domain\Sector\Sector;
use App\ClientManagement\Domain\Sector\SectorRepositoryInterface;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sectors', name: 'app_sector')]
final class SectorController extends AbstractController
{
    public function __construct(
        private SectorService             $manager,
        private SectorRepositoryInterface $repository
    ) {}

    #[Route('', name: 'sector_index', methods: ['GET'])]
    public function index(): Response
    {
        $sectors = $this->repository->findAll();
        return $this->json($sectors, 200, [], ['groups' => 'sector:read']);
    }

    #[Route('/{id}', name: 'sector_show', methods: ['GET'])]
    public function show(Sector $sector): Response
    {
        return $this->json($sector, 200, [], ['groups' => 'sector:read']);
    }

    #[Route('', name: 'sector_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->manager->create($data);
            return $this->json(['message' => 'Sector created'], 201, [], ['groups' => 'sector:read']);
        } catch (\Exception $e){
            return $this->json(['error' => $e->getMessage(), 400]);
        }
    }

    #[Route('/{id}', name: 'sector_update', methods: ['PATCH'])]
    public function update(Sector $sector,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->manager->update($sector, $data);
            return $this->json(['message' => 'Sector udpated'], 200, [], ['groups' => 'sector:read']);
        } catch (\Exception $e){
            return $this->json(['error' => $e->getMessage(), 400]);
        }
    }

    #[Route('/{id}', name: 'sector_delete', methods: ['DELETE'])]
    public function delete(Sector $sector): JsonResponse
    {
        try {
            $this->manager->delete($sector);
            return $this->json(['message' => 'Sector deleted'], 204);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage(), 400]);
        }
    }

}
