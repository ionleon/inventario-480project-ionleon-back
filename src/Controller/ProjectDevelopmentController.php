<?php

namespace App\Controller;

use App\Repository\DevelopmentRepository;
use App\Service\DevelopmentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/{id}/developments', name: 'app_project_users_development')]
final class ProjectDevelopmentController extends AbstractController
{
    public function __construct(
        private DevelopmentManager $devManager,
        private DevelopmentRepository $repository
    )
    {}

    #[Route('', name: 'project_users_development_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('project_development/index.html.twig', [
            'controller_name' => 'ProjectDevelopmentController',
        ]);
    }
}
