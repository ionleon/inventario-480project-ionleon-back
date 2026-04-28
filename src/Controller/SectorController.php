<?php

namespace App\Controller;

use App\Repository\SectorRepository;
use App\Service\SectorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sectors', name: 'app_sector')]
final class SectorController extends AbstractController
{
    public function __construct(
        private SectorManager $manager,
        private SectorRepository $repository
    ) {}

    #[Route('', name: 'sector_index', methods: ['GET'])]
    public function index(): Response
    {
        $sectors = $this->repository->findAll();
        return $this->json($sectors, 200, [], ['groups' => 'sector:read']);
    }
}
