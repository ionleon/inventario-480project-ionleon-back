<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SectorController extends AbstractController
{
    #[Route('/sector', name: 'app_sector')]
    public function index(): Response
    {
        return $this->render('sector/index.html.twig', [
            'controller_name' => 'SectorController',
        ]);
    }
}
