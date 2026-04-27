<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectRoleController extends AbstractController
{
    #[Route('/project/role', name: 'app_project_role')]
    public function index(): Response
    {
        return $this->render('project_role/index.html.twig', [
            'controller_name' => 'ProjectRoleController',
        ]);
    }
}
