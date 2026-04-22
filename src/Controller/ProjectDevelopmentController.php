<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectDevelopmentController extends AbstractController
{
    #[Route('/project/development', name: 'app_project_development')]
    public function index(): Response
    {
        return $this->render('project_development/index.html.twig', [
            'controller_name' => 'ProjectDevelopmentController',
        ]);
    }
}
