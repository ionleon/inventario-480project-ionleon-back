<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TimeEntryController extends AbstractController
{
    #[Route('/time/entry', name: 'app_time_entry')]
    public function index(): Response
    {
        return $this->render('time_entry/index.html.twig', [
            'controller_name' => 'TimeEntryController',
        ]);
    }
}
