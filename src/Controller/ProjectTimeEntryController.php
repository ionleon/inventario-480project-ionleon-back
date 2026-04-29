<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectTimeEntryController extends AbstractController
{
    #[Route('/project/time/entry', name: 'app_project_time_entry')]
    public function index(): Response
    {
        return $this->render('project_time_entry/index.html.twig', [
            'controller_name' => 'ProjectTimeEntryController',
        ]);
    }
}
