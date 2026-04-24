<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DevelopmentController extends AbstractController
{
    #[Route('/development', name: 'app_development')]
    public function index(): Response
    {
        return $this->render('development/index.html.twig', [
            'controller_name' => 'DevelopmentController',
        ]);
    }
}
