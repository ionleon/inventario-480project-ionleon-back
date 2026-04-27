<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TechnologyController extends AbstractController
{
    #[Route('/technology', name: 'app_technology')]
    public function index(): Response
    {
        return $this->render('technology/index.html.twig', [
            'controller_name' => 'TechnologyController',
        ]);
    }
}
